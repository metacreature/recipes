
CREATE TABLE tbl_user (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(32) NOT NULL,
  last_edited  DATETIME NOT NULL,
  cnt_update INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email)
);

CREATE TABLE tbl_category (
  category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (category_id)
);

CREATE TABLE tbl_tag (
  tag_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tag_name VARCHAR(255) NOT NULL,
  locked TINYINT (1) NOT NULL default 0,
  PRIMARY KEY (tag_id),
  UNIQUE KEY (tag_name)
);

CREATE TABLE tbl_ingredients (
  ingredients_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ingredients_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (ingredients_id),
  UNIQUE KEY (ingredients_name)
);

CREATE TABLE tbl_unit (
  unit_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  unit_name VARCHAR(255) NOT NULL,
  locked TINYINT (1) NOT NULL default 0,
  PRIMARY KEY (unit_id),
  UNIQUE KEY (unit_name)
);

CREATE TABLE tbl_recipe (
  recipe_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  recipe_name VARCHAR(255) NOT NULL,
  public TINYINT(1) NOT NULL,
  persons TINYINT UNSIGNED NOT NULL,
  original_text TEXT,
  orig_image_name VARCHAR(255) NOT NULL DEFAULT '',
  image_name VARCHAR(255) NOT NULL DEFAULT '',
  deleted TINYINT(1) NOT NULL default 0,
  last_edited  DATETIME NOT NULL,
  cnt_update INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (recipe_id),
  FOREIGN KEY (category_id) REFERENCES tbl_category (category_id),
  INDEX public (public, user_id)
);

CREATE TABLE tbl_recipe_step (
  recipe_id INT UNSIGNED NOT NULL,
  nr INT NOT NULL,
  step_description text NOT NULL,
  FOREIGN KEY (recipe_id) REFERENCES tbl_recipe (recipe_id)
);

CREATE TABLE tbl_recipe_tag (
  recipe_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (recipe_id) REFERENCES tbl_recipe (recipe_id),
  FOREIGN KEY (tag_id) REFERENCES tbl_tag (tag_id)
);


CREATE TABLE tbl_recipe_ingredients (
  recipe_id INT UNSIGNED NOT NULL,
  ingredients_id INT UNSIGNED NOT NULL,
  nr INT NOT NULL,
  quantity FLOAT NULL,
  unit_id INT UNSIGNED NULL,
  is_alternative TINYINT(1) NOT NULL,
  FOREIGN KEY (recipe_id) REFERENCES tbl_recipe (recipe_id),
  FOREIGN KEY (ingredients_id) REFERENCES tbl_ingredients (ingredients_id),
  FOREIGN KEY (unit_id) REFERENCES tbl_unit (unit_id)
);


INSERT INTO tbl_category (category_name) VALUES ("Cocktail"), ("Suppe"), ("Hauptspeise"), ("Nachspeise"), ("Kekse"), ("Gebäck"), ("Saucen") ;

INSERT INTO tbl_unit (unit_name, locked) VALUES
('Kg', 1),
('dag', 1),
('g', 1),
('l', 1),
('cl', 1),
('ml', 1),
('EL', 1),
('TL', 1),
('Stück', 1),
('Bund', 1),
('Prise', 1),
('Tube', 1),
('Portion', 1),
('Becher', 1),
('Flasche', 1);

INSERT INTO tbl_tag (tag_name, locked) VALUES
('schnell', 1),
('zeitintensiv', 1),
('einfach', 1),
('aufwändig', 1),
('frisch', 1),
('einfrierbar', 1),
('günstig', 1),
('teuer', 1),
('Besuch', 1);