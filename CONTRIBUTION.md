# Contributing Todo & Co

You can contribute to Todo & Co by adding new feature, adding some more useful tests, updating the HTML template or updating the documentation.

## Requirements
*   A github account
*   git installed on your computer ([How to install it](https://docs.github.com/en/get-started/quickstart/set-up-git)).

## Setup Local Project
1.  [Create a fork](https://docs.github.com/en/get-started/quickstart/fork-a-repo) of this project.
2.  Clone the project: https://github.com/riwalenn/Projet-8.git
3.  Install the dependencies : composer install on your console
4.  Open the .env.local.php file and change the database connection values on line 8 like 'DATABASE_URL' => 'mysql://root:@127.0.0.1:3306/oc_projets_n8?serverVersion=5.7.19' for me.
5.  Update the database : php bin/console doctrine:schema:update --force
6.  To have some initial dataset : php bin/console doctrine:fixtures:load
7.  [Create a new branch](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-and-deleting-branches-within-your-repository)

## Tests
1.  Make sure the tests are ok.
2.  If new entity / controller / etc... create new tests, please create tests aligned with the tests already present.

## Commit and create a pull request
1.  Commit your changes ([About commits](https://docs.github.com/en/github/committing-changes-to-your-project/creating-and-editing-commits/about-commits))
2.  [Create a pull request](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request) // [Create a pull request from a fork](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request-from-a-fork) - [About pull request](https://docs.github.com/en/github/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests)
