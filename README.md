# Laravel ShowHeroes passport app

## How to get started

### Clone

Clone passport repository in the directory of your new project (change directory name `new-project` to your project name):
```shell
git clone git@github.com:showheroes/passport.git <new-project>
```

Remove remote passport repository: 
```shell
git remote rm origin
```

Add remote repository of your new project:
```shell
git remote add origin <url>
```

Push the repository
```shell
git push -u origin master
```

### Modify
Update namespaces in composer autoload section and classes.

### Prepare
Execute: `php artisan db:seed --class=ShowHeroesTeamSeeder`


## Users and Teams

Authentication by default is set via Google.
The team of the user is determined from the user email domain.

For example for the users with emails `joe@showheroes.com` 
and `anny@showheroes-group.com` the default team `ShowHeroes` from the seeder `ShowHeroesTeamSeeder`
will be selected, because the for the team is set the primary domain `showheroes.com`
and there are domain aliases in the file `config/app.php`.

> The first user of the team will become a team owner.
