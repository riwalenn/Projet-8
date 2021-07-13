# Projet-8 (Todo & Co)

EN - Projet n°8 created for OpenClassrooms and Back-end Developer Path

## Built With
*   PHP 7.4.9
*   Symfony 4.4

## Download and Installation
You need a web development environment like Wampserver (for windows), MAMP (for Mac) or LAMP (for linux).

*   Clone the project code : "https://github.com/riwalenn/Projet-8.git"
*   Go to the console and write "composer install" where you want to have the project
*   Open the .env.local.php file and change the database connection values on line 8 like 'DATABASE_URL' => 'mysql://root:@127.0.0.1:3306/oc_projets_n8?serverVersion=5.7.19' for me.
*   Update the database : "php bin/console doctrine:schema:update --force"
*   To have some initial dataset : "php bin/console doctrine:fixtures:load"
*   Run the application with "Symfony serve"

## Contribution
*   [How to contribute](https://github.com/riwalenn/Projet-8/blob/main/CONTRIBUTION.md)

## Author
*   **Riwalenn Bas** - *Blog* - [Riwalenn Bas](https://www.riwalennbas.com)
*   **Riwalenn Bas** - *Repositories* - [Github](https://github.com/riwalenn?tab=repositories)