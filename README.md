GitBucket Calendar [![Latest Stable Version](https://poser.pugx.org/hws/gitbucketcalendar/v/stable)](https://packagist.org/packages/hws/gitbucketcalendar) [![Total Downloads](https://poser.pugx.org/hws/gitbucketcalendar/downloads)](https://packagist.org/packages/hws/gitbucketcalendar) [![License](https://poser.pugx.org/hws/gitbucketcalendar/license)](https://packagist.org/packages/hws/gitbucketcalendar)
========================

Library that allows you to display GitHub and BitBucket contributions in GitHub-like calendar widget.


Installation
========================

Code is available in Packagist repository so installation is as simple as including it in your dependencies.


```
"require": {
    "hws/gitbucketcalendar": "1.2.*"
}
```

After that you have to run (and preferably add to your crontab) script from `./examples/cron.php` to fetch data.


Examples
========================


```PHP
$repo = new GitBucketCalendar\GitBucketCalendar([
    'bitbucket_key' => '',
    'bitbucket_secret' => '',
    'bitbucket_commit_usernames' => [
        ''
    ],
    'github_account_username' => '',
    'memcached_host' => 'localhost',
    'memcached_port' => 11211
]);

$repo->printContributionsCalendar();
```

`bitbucket_commit_usernames` contains array with all usernames used for commits that should be counted in.
`github_account_username` is GitHub usernames (exacly like used in profile URL).

![GitBucket](/examples/gitbucket.png?raw=true)