# StopForumSpam
is a nice service from http://www.stopforumspam.com/
this is a little helper to incorporate the daily created csv-database to this application (into a db). And then make it query via CLI.
You can install it on your shared server and let all your projects use it.
That will help to stop spamming the stopforumspam API :)

## TODO
  - refactor commands into classes
  - remove credentials for db
  - better bootstrapping for psc-cms and webforge
  - refactor the db creation into own command
  - create cronjob-command to pull directly file from stopforumspam.com
  
## Howto

  - download a file like listed_email_90_all.zip from stop-forum-spam
  - run php -f bin/cli.php update-emails path/to/listed_email_90_all.zip
    (a lot of dots while processing the sql will be generated)
  - use the out.sql (which is a text file with sql "INSERT INTO" statements) to insert into a "emails" table on your database
  
## Resources

all credits go to [stopforumspam.com](http://www.stopforumspam.com/)