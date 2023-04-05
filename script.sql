----------------------------EFFACER BASE---------------------------

drop table if exists Rue;
drop table if exists Agent;
drop table if exists signalement;
drop table if exists habitant;
drop table if exists eclairage;
drop table if exists signalement_habitant;

----------------------------CREATION TABLES-------------------------
CREATE DOMAIN enum_etat VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('pas réalisé', 'en cours', 'réalisé'));

CREATE DOMAIN enum_agent VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('normal', 'responsable'));

CREATE DOMAIN enum_probleme VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('panne d''éclairage public', 'chaussée abîmée', 'trottoir abîmé', 'égout bouché', 'arbre à tailler', 'voiture ventouse', 'autres'));

CREATE DOMAIN enum_urgence VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('faible', 'moyen', 'élevé', 'très urgent'));

CREATE TABLE Rue (
	id_rue SERIAL PRIMARY KEY,
	nom_rue VARCHAR(50) NOT NULL,
	num_rue_maximum integer
);

CREATE TABLE AGENT (
	id_agent SERIAL PRIMARY KEY,
	login_agent VARCHAR(50) NOT NULL,
	nom_agent VARCHAR(50) NOT NULL,
	prenom_agent VARCHAR(50) NOT NULL,
	mdp_agent VARCHAR(50) NOT NULL,
	type_agent enum_agent DEFAULT 'normal'
);

CREATE TABLE SIGNALEMENT (
	id_signalement SERIAL PRIMARY KEY,
  	probleme enum_probleme NOT NULL,
  	id_rue integer references Rue(id_rue) NOT NULL,
  	numero_maison_proche VARCHAR(10),
  	intervalle_numero_debut VARCHAR(10),
  	intervalle_numero_fin VARCHAR(10),
	description_probleme VARCHAR(500),
	niveau_urgence enum_urgence DEFAULT 'faible',
	date_signalement DATE not null default CURRENT_DATE,
	compteur_signalement_total integer DEFAULT 1,
	compteur_signalement_anonyme integer DEFAULT 0,
	etat enum_etat DEFAULT 'pas réalisé',
	description_resolution VARCHAR(500),
	date_modification DATE not null default CURRENT_DATE,
	id_agent integer references AGENT(id_agent)
);

CREATE TABLE HABITANT (
	id_habitant SERIAL PRIMARY KEY,
	nom_habitant VARCHAR(50) NOT NULL,
	prenom_habitant VARCHAR(50) NOT NULL,
	id_rue integer references Rue(id_rue) NOT NULL,
	num_adresse_habitant VARCHAR(10) NOT NULL,
	numero_portable VARCHAR(25) NOT NULL,
	numero_fixe VARCHAR(25),
	mail VARCHAR(100)
);

CREATE TABLE ECLAIRAGE (
	id_eclairage SERIAL PRIMARY KEY,
	id_rue integer references Rue(id_rue) NOT NULL,
	date_heure_debut timestamp NOT NULL,
	date_heure_fin timestamp NOT NULL
);

CREATE TABLE SIGNALEMENT_HABITANT (
	id_signalement integer references SIGNALEMENT(id_signalement),
	id_habitant integer references HABITANT(id_habitant)
);

ALTER TABLE SIGNALEMENT_HABITANT ADD CONSTRAINT PK PRIMARY KEY (id_signalement, id_habitant);
	


--Contrainte 1
/*Cette contrainte s'assure que chaque signalement a bien une valeur pour les colonnes "probleme", "niveau_urgence" et "date_signalement"
 * Si l'une de ces colonnes est manquante, l'insertion ou la mise à jour sera refusée.*/

ALTER TABLE SIGNALEMENT ADD CONSTRAINT contrainte_signalement
CHECK (probleme IS NOT NULL AND niveau_urgence IS NOT NULL AND date_signalement IS NOT NULL);

--Contrainte 2
/*Cette contrainte utilise une fonction plpgsql et un trigger pour incrémente le compteur du signalement originel si un nouveau signalement est identique au précédent
 * La comparaison des signalements est basée sur les valeurs des colonnes probleme, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme et niveau_urgence. Si les deux signalements sont identiques
 * la fonction met à jour le compteur du signalement originel et annule l'insertion du nouveau signalement*/

CREATE OR REPLACE FUNCTION increment_compteur_signalement()
RETURNS trigger AS $$
declare 
	last_signalement INTEGER;
BEGIN
  -- On recherche le dernier signalement sur cette même rue
  SELECT INTO last_signalement * FROM signalement WHERE id_rue = NEW.id_rue ORDER BY date_signalement DESC LIMIT 1;

  -- Si le dernier signalement est identique au nouveau, on incrémente le compteur
  IF last_signalement IS NOT NULL AND last_signalement.probleme = NEW.probleme AND last_signalement.numero_maison_proche = NEW.numero_maison_proche AND last_signalement.intervalle_numero_debut = NEW.intervalle_numero_debut AND last_signalement.intervalle_numero_fin = NEW.intervalle_numero_fin AND last_signalement.description_probleme = NEW.description_probleme AND last_signalement.niveau_urgence = NEW.niveau_urgence THEN
    UPDATE signalement SET compteur_signalement_total = last_signalement.compteur_signalement_total + 1 WHERE id_signalement = last_signalement.id_signalement;
    RETURN NULL; -- On annule l'insertion du nouveau signalement
  END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER increment_compteur_signalement_trigger
  BEFORE INSERT ON signalement
  FOR EACH ROW
  EXECUTE FUNCTION increment_compteur_signalement();

 --Contrainte 3
 /*Cette contrainte permet donc de détecter les signalements identiques dans un intervalle d'un an
  * et d'incrémenter le niveau d'urgence du signalement original si le nombre de signalements identiques dépasse 10.*/
 
 CREATE OR REPLACE FUNCTION increment_urgence() RETURNS TRIGGER AS $$
DECLARE
    nb_similar INTEGER;
    max_id INTEGER;
BEGIN
    SELECT COUNT(*) INTO nb_similar FROM signalement WHERE probleme = NEW.probleme AND niveau_urgence = NEW.niveau_urgence 
        AND description_probleme = NEW.description_probleme AND id_rue = NEW.id_rue 
        AND numero_maison_proche = NEW.numero_maison_proche AND intervalle_numero_debut = NEW.intervalle_numero_debut 
        AND intervalle_numero_fin = NEW.intervalle_numero_fin AND etat = 'pas réalisé' 
        AND id_signalement != NEW.id_signalement AND date_signalement >= (CURRENT_DATE - INTERVAL '1 year');

    IF nb_similar >= 9 THEN
        SELECT MAX(id_signalement) INTO max_id FROM signalement WHERE probleme = NEW.probleme AND niveau_urgence = NEW.niveau_urgence 
            AND description_probleme = NEW.description_probleme AND id_rue = NEW.id_rue 
            AND numero_maison_proche = NEW.numero_maison_proche AND intervalle_numero_debut = NEW.intervalle_numero_debut 
            AND intervalle_numero_fin = NEW.intervalle_numero_fin AND etat = 'pas réalisé' 
            AND date_signalement >= (CURRENT_DATE - INTERVAL '1 year');
        UPDATE signalement SET niveau_urgence = 'très urgent' WHERE id_signalement = max_id;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER increment_urgence_trigger
AFTER INSERT ON signalement
FOR EACH ROW
EXECUTE FUNCTION increment_urgence();

--Contrainte 4
/*Ce déclencheur s'exécute après chaque mise à jour de la table "signalement" où le champ "principal_id" est défini
 * Il compare le signalement mis à jour (NEW) avec celui référencé par "principal_id" (OLD) et s'il sont identiques, il incrémente le compteur du signalement principal et supprime le signalement secondaire
 * La mise à jour du signalement principal et la suppression du secondaire sont effectuées dans une transaction
 * ce qui garantit que les deux opérations réussissent ou échouent ensemble*/

CREATE OR REPLACE FUNCTION fusionner_signalement()
RETURNS TRIGGER AS $$
BEGIN
    -- Vérifier si le secondaire est identique au principal
    IF NEW.probleme = OLD.probleme AND NEW.niveau_urgence = OLD.niveau_urgence AND NEW.date_signalement = OLD.date_signalement THEN
        -- Incrémenter le compteur du principal
        UPDATE signalement SET compteur = compteur + NEW.compteur WHERE id = OLD.id;
        -- Supprimer le secondaire
        DELETE FROM signalement WHERE id = NEW.id;
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER fusionner_signalement_trigger
AFTER UPDATE ON signalement
FOR EACH ROW
WHEN (NEW.principal_id IS NOT NULL)
EXECUTE FUNCTION fusionner_signalement();

--Contrainte 5
/*Cette fonction trigger mettra à jour la colonne "derniere_modification" de chaque ligne modifiée dans la table "signalement" avec la date et l'heure actuelles.*/

CREATE OR REPLACE FUNCTION maj_derniere_modification()
RETURNS TRIGGER AS $$
BEGIN
    NEW.derniere_modification = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER maj_signalement
BEFORE UPDATE ON signalement
FOR EACH ROW
EXECUTE FUNCTION maj_derniere_modification();

--Contrainte 6
/*Cette contrainte vérifie que l'état du signalement est soit "En cours", soit "Ancien" avec une date de modification inférieure à trois mois à partir de la date et l'heure actuelles.*/

ALTER TABLE signalement ADD CONSTRAINT chk_probleme_en_cours_ou_anciens_trois_mois 
CHECK (etat = 'En cours' OR (etat = 'Ancien' AND date_modification >= NOW() - INTERVAL '3 months'));

--Contrainte 7

ALTER TABLE actions
ADD COLUMN nom_agent varchar(255) NOT NULL;

--Contrainte 9
/*Cette contrainte est basée sur une vérification de la colonne demande_ts, qui stocke la date et l'heure de la demande de prolongation d'éclairage
 * La contrainte chk_demande_temps vérifie que la date et l'heure de la demande sont supérieures à la date et l'heure actuelles moins cinq minutes (soit les cinq dernières minutes)
 * Si la condition n'est pas remplie, la contrainte empêchera l'insertion de la nouvelle demande dans la table.*/

CREATE TABLE ma_table (
   id SERIAL PRIMARY KEY,
   demande_ts TIMESTAMP NOT NULL,
   -- Autres colonnes
   CONSTRAINT chk_demande_temps CHECK (demande_ts > NOW() - INTERVAL '5 minutes')
);

--procedure(ne renvoie rien en sortie) pour insérer une ligne habitant+signalement_habitant
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


INSERT INTO Rue (nom_rue, num_rue_maximum)  VALUES
	 ('rue des Miagistes', 58),
	 ('rue de la Monnaie',12),
	 ('rue de la Monnaie',47),
	 ('Place André Maginot ',56),
	 ('Place Carnot',27),
	 ('Rue Saint Julien',70),
	 ('Rue Sonnini ',43);

INSERT INTO AGENT (nom_agent, prenom_agent, mdp_agent, type_agent) VALUES
	 ('T0t0', 'Toto','toto','bhjcguhvbzvcjeci','normal'),
	 ('T4t4', 'Tata','tata','icjsiojhcku','responsable'),
	 ('4lice', 'Alice','alice','dnsjchziscze','responsable'),
	 ('B0b', 'Bob','bob','dqsqdqs','normal');
	
INSERT INTO HABITANT (nom_habitant, prenom_habitant, id_rue, num_adresse_habitant, numero_portable, numero_fixe, mail) VALUES
	 ('Zadig','Géraldine',1, '21 bis', '0666666666', NULL,'geraldine.zadig@gmail.com');


INSERT INTO signalement (probleme, id_rue, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme, date_signalement) VALUES
	 ('égout bouché',1,NULL,'3 bis','13',NULL,'2023-05-23');

INSERT INTO eclairage (id_rue,date_heure_debut,date_heure_fin) VALUES
	 (1, '2023-03-23 15:00:00','2023-03-23 15:15:00');

INSERT INTO signalement_habitant (id_signalement,id_habitant) VALUES
	 (1,1);