# TROUBLESHOOTING file_put_contents "PERMISSION DENIED"
chmod 666 /Applications/XAMPP/xamppfiles/htdocs/sprint3_json/app/models/data/tasks.json

# GIT HISTORY RETRIEVAL

Sequence to retrieve and graph ALL commit history from all branches, including those deleted locally (git) and/or remotely (GitHub):

1. Fetch all branches and prune deleted remote branches:
git fetch --all --prune
2. View all branches, including remote ones:
git branch -a
3. Check the reflog for all recent actions, including work on deleted branches:
git reflog
4. Graph all commits, including those from deleted branches:
git log --graph --oneline --decorate $(git rev-list --all --parents --color)
5. For a more detailed view with author information:
git log --graph --pretty=format:'%C(auto)%h%d %s %C(blue)<%an>' --all
6. To see merge commits, which may include names of deleted branches:
git log --merges

This sequence should provide a comprehensive view of the repository's history, including deleted branches. If you need to restore a deleted branch, you can use the SHA1 from the reflog output.
For GitHub-specific actions:
7. On GitHub, go to the repository's "Activity" tab, select "Branch deletions", and use the restore option if available.

Remember, while these steps will show most of the history, some information about deleted branches may eventually be lost if they're not merged and are removed from the reflog.


-----------
READ, DECIDE AND MERGE ABOVE :
To see the work through branches with decorations in Git :
git log --graph --oneline --decorate --all

For a more detailed view, you can use:
git log --graph --pretty=format:'%C(auto)%h%d %s %C(blue)<%an>' --all
This command customizes the output to include commit hashes, decorations, commit messages, and author names with color coding.

If you're not seeing all feature branches, it might be because:
    Some branches are not fetched from the remote repository.
    Some branches have been deleted locally or remotely.
    The branches exist but haven't diverged from the main branch.
Steps :
git fetch --all
git branch -a
git log --graph --oneline --decorate --all --simplify-by-decoration

If you're not seeing the log of all feature branches you've worked with,
it's because Git's log doesn't show deleted branches by default.
Steps :
git reflog
git log --walk-reflogs --all
git log --graph --oneline --decorate $(git rev-list --all --parents --color)
These commands will show you the history of your work, including on branches that have been deleted after merging.



# PHP initial Project
Main structure of php project. Folders / files:
- **app**
  - **controllers**
  - **models**
  - **views**
- **config**
- **lib**
  - **base**
- **web**

### Usage

The web/index.php is the heart of the system.
This means that your web applications root folder is the “web” folder.

All requests go through this file and it decides how the routing of the app
should be.
You can add additional hooks in this file to add certain routes.

### Project Structure

The root of the project holds a few directories:
**/app** This is the folder where your magic will happen. Use the views, controllers and models folder for your app code.
**/config** this folder holds a few configuration files. Currently only the connection to the database.
**/lib** This is where you should put external libraries and other external files.
**/lib/base** The library files. Don’t change these :)
**/web** This folder holds files that are to be “downloaded” from your app. Stylesheets, javascripts and images used. (and more of course)

The system uses a basic MVC structure, with your web app’s files located in the
“app” folder.

#### app/controllers
Your application’s controllers should be defined here.

All controller names should end with “Controller”. E.g. TestController.
All controllers should inherit the library’s “Controller” class.
However, you should generally just make an ApplicationController, which extends
the Controller. Then you can defined beforeFilters etc in that, which will get run
at every request.

#### app/models
Models handles database interaction etc.

All models should inherit from the Model class, which provides basic functionality.
The Model class handles basic functionality such as:

Setting up a database connection (using PDO)
fetchOne(ID)
save(array) → both update/create
delete(ID)
app/views
Your view files.
The structure is made so that having a controller named TestController, it looks
in the app/views/test/ folder for it’s view files.

All view files end with .phtml
Having an action in the TestController called index, the view file
app/views/test/index.phtml will be rendered as default.

#### config/routes.php
Your routes around the system needs to be defined here.
A route consists of the URL you want to call + the controller#action you want it
to hit.

An example is:
$routes = array(
‘/test’ => ‘test#index’ // this will hit the TestController’s indexAction method.
);

#### Error handling
A general error handling has been added.

If a route doesn’t exist, then the error controller is hit.
If some other exception was thrown, the error controller is hit.
As default, the error controller just shows the exception occured, so remember
to style the error controller’s view file (app/views/error/error.phtml)


### Utilities
- [PHP Developers Guide](https://www.php.net/manual/en/index.php).
- .gitignore file configuration. [See Official Docs](https://docs.github.com/en/get-started/getting-started-with-git/ignoring-files).
- Git branches. [See Official Docs](https://git-scm.com/book/en/v2/Git-Branching-Branches-in-a-Nutshell).
