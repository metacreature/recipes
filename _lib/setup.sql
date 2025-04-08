
CREATE TABLE tbl_user (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(32) NOT NULL,
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
  PRIMARY KEY (tag_id)
);

CREATE TABLE tbl_ingredients (
  ingredients_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ingredients_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (ingredients_id)
);

CREATE TABLE tbl_recipe (
  recipe_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  recipe_name VARCHAR(255) NOT NULL,
  public TINYINT(1) NOT NULL,
  persons TINYINT UNSIGNED DEFAULT NULL,
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
  quantity FLOAT NOT NULL,
  unit VARCHAR(255) NOT NULL,
  is_alternative TINYINT(1) NOT NULL,
  FOREIGN KEY (recipe_id) REFERENCES tbl_recipe (recipe_id),
  FOREIGN KEY (ingredients_id) REFERENCES tbl_ingredients (ingredients_id)
);


INSERT INTO tbl_category (category_name) VALUES ("Cocktail"), ("Suppe"), ("Hauptgericht"), ("Nachspeise");