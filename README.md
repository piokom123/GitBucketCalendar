GitBucket Calendar
========================

Library that allows you to display GitHub and BitBucket contributions in GitHub-like calendar widget.


Installation
========================

Code is available in Packagist repository so installation is as simple as including it in your dependencies.


```
"require": {
    "hws/gitbucketcalendar": "1.*"
}
```


Examples
========================


```PHP
$repo = new GitBucketCalendar\GitBucketCalendar([
    'bitbucket_key' => '',
    'bitbucket_secret' => '',
    'bitbucket_commit_usernames' => [
        ''
    ],
    'github_account_username' => ''
]);

$repo->printContributionsCalendar();
```

`bitbucket_commit_usernames` contains array with all usernames used for commits that should be counted in.
`github_account_username` is GitHub usernames (exacly like used in profile URL).