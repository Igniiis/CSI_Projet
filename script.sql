----------------------------EFFACER BASE---------------------------

drop table if exists signalement_habitant;
drop table if exists habitant;
drop table if exists eclairage;
drop table if exists signalement;
drop table if exists Rue;
drop table if exists Agent;

drop domain if exists enum_etat;
drop domain if exists enum_agent;
drop domain if exists enum_probleme;
drop domain if exists enum_urgence;

----------------------------CREATION DOMAINES-------------------------
CREATE DOMAIN enum_etat VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('pas réalisé', 'en cours', 'réalisé'));

CREATE DOMAIN enum_agent VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('normal', 'responsable'));

CREATE DOMAIN enum_probleme VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('panne d''éclairage public', 'chaussée abîmée', 'trottoir abîmé', 'égout bouché', 'arbre à tailler', 'voiture ventouse', 'autres'));

CREATE DOMAIN enum_urgence VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('faible', 'moyen', 'élevé', 'très urgent'));

----------------------------CREATION TABLES------------------------

CREATE TABLE Rue (
	id_rue SERIAL PRIMARY KEY,
	nom_rue VARCHAR(50) NOT NULL,
	num_rue_maximum integer
);

CREATE TABLE AGENT (
	id_agent VARCHAR(50) NOT NULL PRIMARY KEY,
	nom_agent VARCHAR(50) NOT NULL,
	prenom_agent VARCHAR(50) NOT NULL,
	mdp_agent VARCHAR(50) NOT NULL,
	type_agent enum_agent DEFAULT 'normal'
);

CREATE TABLE SIGNALEMENT (
	id_signalement SERIAL PRIMARY KEY,
  	probleme enum_probleme NOT NULL,
  	id_rue integer references Rue(id_rue) NOT NULL,
  	numero_maison_proche integer,
  	intervalle_numero_debut integer,
  	intervalle_numero_fin integer,
	description_probleme VARCHAR(500),
	niveau_urgence enum_urgence DEFAULT 'faible',
	date_signalement DATE DEFAULT CURRENT_DATE,
	compteur_signalement_total integer DEFAULT 1,
	compteur_signalement_anonyme integer DEFAULT 0,
	etat enum_etat DEFAULT 'pas réalisé',
	description_resolution VARCHAR(500),
	date_modification DATE not null default CURRENT_DATE,
	id_agent VARCHAR(50) references AGENT(id_agent)
);

CREATE TABLE HABITANT (
	id_habitant SERIAL PRIMARY KEY,
	nom_habitant VARCHAR(50) NOT NULL,
	prenom_habitant VARCHAR(50) NOT NULL,
	id_rue integer references Rue(id_rue) NOT NULL,
	num_adresse_habitant VARCHAR(10) NOT NULL,
	numero_portable VARCHAR(25),
	numero_fixe VARCHAR(25),
	mail VARCHAR(100) NOT NULL
);

CREATE TABLE ECLAIRAGE (
	id_eclairage SERIAL PRIMARY KEY,
	id_rue integer references Rue(id_rue) NOT NULL,
	date_heure_debut timestamp NOT NULL,
	date_heure_fin timestamp NOT NULL
);

CREATE TABLE SIGNALEMENT_HABITANT (
	id_signalement integer NOT NULL references SIGNALEMENT(id_signalement),
	id_habitant integer NOT NULL references HABITANT(id_habitant)
);




---------------------------- CREATION CONTRAINTES -------------------------


--Ajouter la double clé primaire pour la table
ALTER TABLE SIGNALEMENT_HABITANT ADD CONSTRAINT PK PRIMARY KEY (id_signalement, id_habitant);


/*Cette contrainte utilise une fonction plpgsql et un trigger pour incrémente le compteur du signalement originel si un nouveau signalement est identique au précédent
 * La comparaison des signalements est basée sur les valeurs des colonnes probleme, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme et niveau_urgence. Si les deux signalements sont identiques
 * la fonction met à jour le compteur du signalement originel et annule l'insertion du nouveau signalement*/
CREATE OR REPLACE FUNCTION increment_compteur_signalement()
RETURNS TRIGGER AS $$
DECLARE
	last_signalement INTEGER;
    t INTEGER;
BEGIN

	SELECT count(*) INTO t FROM Signalement WHERE
    SIGNALEMENT.probleme=NEW.probleme AND
    SIGNALEMENT.id_rue = NEW.id_rue AND
    SIGNALEMENT.numero_maison_proche = NEW.numero_maison_proche AND
    SIGNALEMENT.intervalle_numero_debut = NEW.intervalle_numero_debut AND
    SIGNALEMENT.intervalle_numero_fin = NEW.intervalle_numero_fin;


	-- Si le dernier signalement est identique au nouveau, on incrémente le compteur
	IF (t=1)
		THEN
	    	UPDATE signalement
	    	SET compteur_signalement_total = (SELECT compteur_signalement_total FROM signalement WHERE id_signalement = last_signalement) + 1,
	    	    compteur_signalement_anonyme = (SELECT compteur_signalement_total FROM signalement WHERE id_signalement = last_signalement) + NEW.compteur_signalement_anonyme
	    	WHERE id_signalement = last_signalement;
	    	RETURN NULL; -- On annule l'insertion du nouveau signalement
	END IF;
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER increment_compteur_signalement_trigger
	BEFORE INSERT ON signalement
  	FOR EACH ROW
  	EXECUTE FUNCTION increment_compteur_signalement();



 /*Cette contrainte permet d'incrémeter le niveau d'urgence en fonction du nombre qu'affiche le compteur.*/
 CREATE OR REPLACE FUNCTION increment_urgence() RETURNS TRIGGER AS $$
DECLARE
    nb_compteur INTEGER;
    max_id INTEGER;
BEGIN

    --on récupère le nouveau compteur total
    SELECT compteur_signalement_total INTO nb_compteur FROM SIGNALEMENT WHERE id_signalement=NEW.id_signalement;

    --    (VALUE IN ('faible', 'moyen', 'élevé', 'très urgent'));
    IF(nb_compteur<=9)
    then
        UPDATE SIGNALEMENT SET niveau_urgence='faible' WHERE SIGNALEMENT.id_signalement=NEW.id_signalement;
    elseif (nb_compteur <= 19)
    then
        UPDATE SIGNALEMENT SET niveau_urgence='moyen' WHERE SIGNALEMENT.id_signalement=NEW.id_signalement;
    elseif (nb_compteur <= 29)
    then
        UPDATE SIGNALEMENT SET niveau_urgence='élevé' WHERE SIGNALEMENT.id_signalement=NEW.id_signalement;
    elseif (nb_compteur >= 30)
    then
        UPDATE SIGNALEMENT SET niveau_urgence='très urgent' WHERE SIGNALEMENT.id_signalement=NEW.id_signalement;
    else
        --erreur
        RETURN NULL;
    end if;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER increment_urgence_trigger
	AFTER UPDATE ON signalement
	FOR EACH ROW
	EXECUTE FUNCTION increment_urgence();



/* Cette procédure permet à un utilisateur de faire fusionner 2 signalements qu'il trouve similaire*/
CREATE OR REPLACE PROCEDURE fusion_signalement(id_disparant integer, id_recuperant integer)
AS $$
DECLARE

BEGIN

    -- on rajoute au signalement récupérant les valeurs de compteur du signalement disparant
    UPDATE SIGNALEMENT SET compteur_signalement_total=(SELECT compteur_signalement_total FROM SIGNALEMENT WHERE id_signalement=id_disparant) + (SELECT compteur_signalement_total FROM SIGNALEMENT WHERE id_signalement=id_recuperant),
    compteur_signalement_anonyme=(SELECT compteur_signalement_anonyme FROM SIGNALEMENT WHERE id_signalement=id_disparant) + (SELECT compteur_signalement_anonyme FROM SIGNALEMENT WHERE id_signalement=id_recuperant)
    WHERE id_signalement=id_recuperant;

    -- on supprime maintenant ce signalement qui n'a plus d'utilité
    DELETE FROM SIGNALEMENT WHERE id_signalement=id_disparant;
END;
$$ LANGUAGE plpgsql;




/*Cette fonction trigger mettra à jour la colonne "derniere_modification" de chaque ligne modifiée dans la table "signalement" avec la date et l'heure actuelles.*/
CREATE OR REPLACE PROCEDURE maj_derniere_modification(id_sig integer, id_a varchar(50))
AS $$
BEGIN

    UPDATE SIGNALEMENT SET date_modification = current_date,
    id_agent = id_a WHERE id_signalement=id_sig;
END;
$$ LANGUAGE plpgsql;


/*Cette vue permet de n'afficher que les signalements qui ont moins de 3 mois (pour les habitants) .*/
CREATE OR REPLACE VIEW signalement_3mois AS
    SELECT * FROM SIGNALEMENT WHERE date_modification >= CURRENT_DATE - INTERVAL '3 months' ORDER BY date_modification;


/* Cette contrainte permet d'inserer une nouvelle ligne d'éclairage, et ceux seulement si on est dans les 5 dernières minutes
 * de l'éclairage en cours ou si il n'y a tout simplement pas d'éclairage en cours
 * @return un nombre utilisé par le site et php pour prévenir l'habitant */
CREATE OR REPLACE FUNCTION func_insert_eclaire(id integer) RETURNS integer
AS $$
DECLARE
    t integer;
    temps_fin timestamp;
    temps_test timestamp;
BEGIN
    temps_test := CURRENT_TIMESTAMP AT TIME ZONE 'Europe/Paris';
    --on récupère le nombre d'éclairage en cours
    SELECT COUNT(*) INTO t FROM Eclairage WHERE Eclairage.id_rue=id AND Eclairage.date_heure_fin>(temps_test);

    IF(t=0)--si il n'y a pas d'éclairage en cours ou moins de 5 minutes pour la rue donnée
    then
        INSERT INTO Eclairage (id_rue, date_heure_debut, date_heure_fin) VALUES (id, temps_test, (temps_test+INTERVAL '15 Minutes'));
        return 2;
    end if;

    --on vérifie maintenant si il reste à l'éclairage en cours plus ou moins de 5 minutes
    SELECT COUNT(*) INTO t FROM Eclairage WHERE id_rue=id AND Eclairage.date_heure_fin>(temps_test+INTERVAL'5 Minutes');

    IF(t=0)--si il n'y a pas d'éclairage en cours ou moins de 5 minutes pour la rue donnée
    then
        --TODO
        --insert modifiant la date pour prendre celle de fin de l'ancienne éclairage comme nouvelle date de début
        SELECT date_heure_fin into temps_fin FROM ECLAIRAGE where id_rue=id;
        INSERT INTO Eclairage (id_rue, date_heure_debut, date_heure_fin) VALUES (id, temps_fin, (temps_fin+INTERVAL '15 Minutes'));
        return 1;
    end if;

    --cela signifit que l'éclairage en cours sera fini dans plus de 5 minutes donc nous ne pouvons pas insérer de un nouvelle éclairage
    return 0;
END;
$$ LANGUAGE plpgsql;


--Procedure pour insérer une ligne habitant+signalement_habitant
CREATE OR REPLACE PROCEDURE proc_insert_habitant_signalement
(id_sig integer, nom_h VARCHAR(50),prenom_h VARCHAR(50),id_r integer, num_adresse_h VARCHAR(10), numero_p VARCHAR(25), numero_f VARCHAR(25), mail_h VARCHAR(100))
AS $$
DECLARE
    nb integer;
BEGIN
    SELECT COUNT(*) INTO nb FROM Habitant WHERE Habitant.nom_habitant=nom_h AND Habitant.prenom_habitant=prenom_h AND Habitant.mail=mail_h AND Habitant.id_rue=id_r;

    IF(nb=0) --Si on a pas de ligne avec toutes les coordonnées identiques, cela signifit que l'on a jamais ajouté cet habitant
    THEN -- alors on insert les valeur de l'habitant en parametre
        INSERT INTO habitant (nom_habitant, prenom_habitant, id_rue, num_adresse_habitant, numero_portable, numero_fixe, mail) VALUES (nom_h, prenom_h, id_r, num_adresse_h, numero_p, numero_f, mail_h);
    END IF;

    --on récupère l'id de l'habitant concerné
    SELECT id_habitant INTO nb FROM Habitant WHERE Habitant.nom_habitant=nom_h AND Habitant.prenom_habitant=prenom_h AND Habitant.mail=mail_h AND Habitant.id_rue=id_r;

    --et on insert dans la table de liaison les 2 ids
    INSERT INTO signalement_habitant (id_signalement, id_habitant) VALUES (id_sig,nb);
END;
$$ LANGUAGE PLPGSQL;



-------------------INSERTION--------------------------
INSERT INTO Rue (nom_rue, num_rue_maximum) VALUES
	 ('rue des Miagistes', 58),
	 ('rue de la Monnaie',47),
	 ('Place André Maginot ',56),
	 ('Place Carnot',27),
	 ('Rue Saint Julien',70),
	 ('Rue Sonnini ',43);

INSERT INTO AGENT (id_agent, nom_agent, prenom_agent, mdp_agent, type_agent) VALUES
	 ('guigui', 'Chartier', 'Guillaume', 'admin', 'responsable'),
	 ('coco', 'Mayer', 'Chloé', 'admin', 'responsable'),
	 ('mama', 'Malleret', 'Maxence', 'admin', 'responsable'),
	 ('ag1', 'nomAgent1', 'prenomAgent2','admin', 'normal'),
	 ('ag2', 'nomAgent2', 'prenomAgent2','admin', 'normal');

INSERT INTO HABITANT (nom_habitant, prenom_habitant, id_rue, num_adresse_habitant, numero_portable, numero_fixe, mail) VALUES
	 ('Zadig','Géraldine',1, '21 bis', '0666666666', NULL, 'geraldine.zadig@gmail.com');


INSERT INTO signalement (probleme, id_rue, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme, date_signalement) VALUES
	 ('égout bouché', 1, NULL, 3, 13, NULL, '2023-01-01');

INSERT INTO eclairage (id_rue,date_heure_debut,date_heure_fin) VALUES
	 (1, '2023-03-23 15:00:00','2023-03-23 15:15:00');

INSERT INTO signalement_habitant (id_signalement,id_habitant) VALUES
	 (1,1);