----------------------------CREATION BASE---------------------------




----------------------------CREATION TABLES-------------------------
CREATE DOMAIN enum_etat VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('pas réalisé', 'en cours', 'réalisé'));

CREATE DOMAIN enum_agent VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('normal', 'responsable'));

CREATE DOMAIN enum_etat VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('panne d''éclairage public', 'chaussée abîmée', 'trottoir abîmé', 'égout bouché', 'arbre à tailler', 'voiture ventouse', 'autres'));

CREATE DOMAIN enum_etat VARCHAR(30) NOT NULL CHECK
    (VALUE IN ('faible', 'moyen', 'élevé', 'très urgent'));

CREATE TABLE Rue (
	id_rue SERIAL PRIMARY KEY,
	nom_rue VARCHAR(50) NOT NULL,
	num_rue_maximum integer
);

CREATE TABLE AGENT (
	id_agent SERIAL PRIMARY KEY,
	nom_agent VARCHAR(50) NOT NULL,
	prenom_agent VARCHAR(50) NOT NULL,
	mdp_agent VARCHAR(5O) NOT NULL,
	type_agent type_agent DEFAULT 'normal'
);

CREATE TABLE SIGNALEMENT (
	id_signalement SERIAL PRIMARY KEY,
  	probleme probleme NOT NULL,
  	id_rue integer references Rue(id_rue) NOT NULL,
  	numero_maison_proche VARCHAR(10),
  	intervalle_numero_debut VARCHAR(10),
  	intervalle_numero_fin VARCHAR(10),
	description_probleme VARCHAR(500),
	niveau_urgence urgence DEFAULT 'faible',
	date_signalement date NOT NULL,
	compteur_signalement_total integer DEFAULT 1,
	compteur_signalement_anonyme integer DEFAULT 0,
	etat enum_etat DEFAULT 'pas réalisé',
	date_modification date,
	description_resolution VARCHAR(500),
	date_resolution date,
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
	 ('Toto','toto','bhjcguhvbzvcjeci','normal'),
	 ('Tata','tata','icjsiojhcku','responsable'),
	 ('Alice','alice','dnsjchziscze','responsable'),
	 ('Bob','bob','dqsqdqs','normal');
	
INSERT INTO HABITANT (nom_habitant, prenom_habitant, id_rue, num_adresse_habitant, numero_portable, numero_fixe, mail) VALUES
	 ('Zadig','Géraldine',1, '21 bis', '0666666666', NULL,'geraldine.zadig@gmail.com');


INSERT INTO signalement (probleme, id_rue, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme, date_signalement) VALUES
	 ('égout bouché',1,NULL,'3 bis','13',NULL,'2023-05-23');

INSERT INTO eclairage (id_rue,date_heure_debut,date_heure_fin) VALUES
	 (1, '2023-03-23 15:00:00','2023-03-23 15:15:00');

INSERT INTO signalement_habitant (id_signalement,id_habitant) VALUES
	 (1,1);
