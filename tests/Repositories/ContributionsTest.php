<?php

use PHPUnit\Framework\TestCase;

use GitBucketCalendar\GitBucketCalendar;

class ContributionsTest extends TestCase {
    private $bitBucketMockBuilder = null;
    private $gitHubMockBuilder = null;
    private $bitBucketFetcher;
    private $gitHubFetcher;

    protected function setUp() {
        if ($this->bitBucketMockBuilder === null) {
            $this->bitBucketFetcher = $this->getMockBuilder('\GitBucketCalendar\Repositories\Fetchers\BitBucketFetcher')
                ->disableOriginalConstructor()
                ->getMock();

            $this->gitHubFetcher = $this->getMockBuilder('\GitBucketCalendar\Repositories\Fetchers\GitHubFetcher')
                ->disableOriginalConstructor()
                ->getMock();

            $this->bitBucketFetcher->method('getCommits')
                ->will($this->returnCallback([$this, 'getCommitsMock']));

            $this->bitBucketFetcher->method('getIssues')
                ->will($this->returnCallback([$this, 'getIssuesMock']));

            $this->bitBucketMockBuilder = $this->getMockBuilder('\GitBucketCalendar\Repositories\BitBucket')
                ->enableProxyingToOriginalMethods();

            $this->gitHubMockBuilder = $this->getMockBuilder('\GitBucketCalendar\Repositories\GitHub')
                ->enableProxyingToOriginalMethods();
        }
    }

    public function testGetContributions() {
        $gitBucket = $this->prepareTestGetContributions();

        $this->assertEquals($gitBucket->getContributions(), [
            'contributions' => [
                '2017-01-01' => 0,
                '2017-01-02' => 0,
                '2017-01-03' => 0,
                '2017-01-04' => 0,
                '2017-01-05' => 0,
                '2017-01-06' => 11,
                '2017-01-07' => 7,
                '2017-01-08' => 3,
                '2017-01-09' => 12,
                '2017-01-10' => 2
            ],
            'latestStreak' => 5,
            'latestStreakStart' => '2017-01-06',
            'latestStreakEnd' => '2017-01-10',
            'longestStreak' => 5,
            'longestStreakStart' => '2017-01-06',
            'longestStreakEnd' => '2017-01-10',
            'contributionsSum' => 35,
            'maxValue' => 12,
            'steps' => [
                9 => '#1e6823',
                6 => '#44a340',
                3 => '#8cc665',
                1 => '#d6e685'
            ]
        ]);
    }

    public function testStreakWithoutCurrentDay() {
        $gitBucket = $this->prepareTestStreakWithoutCurrentDay();

        $this->assertEquals($gitBucket->getContributions(), [
            'contributions' => [
                '2017-01-01' => 0,
                '2017-01-02' => 0,
                '2017-01-03' => 0,
                '2017-01-04' => 0,
                '2017-01-05' => 0,
                '2017-01-06' => 11,
                '2017-01-07' => 7,
                '2017-01-08' => 3,
                '2017-01-09' => 12,
                '2017-01-10' => 0
            ],
            'latestStreak' => 4,
            'latestStreakStart' => '2017-01-06',
            'latestStreakEnd' => '2017-01-09',
            'longestStreak' => 4,
            'longestStreakStart' => '2017-01-06',
            'longestStreakEnd' => '2017-01-09',
            'contributionsSum' => 33,
            'maxValue' => 12,
            'steps' => [
                9 => '#1e6823',
                6 => '#44a340',
                3 => '#8cc665',
                1 => '#d6e685'
            ]
        ]);
    }

    public function testBrokenStreak() {
        $gitBucket = $this->prepareTestBrokenStreak();

        $this->assertEquals($gitBucket->getContributions(), [
            'contributions' => [
                '2017-01-01' => 0,
                '2017-01-02' => 0,
                '2017-01-03' => 0,
                '2017-01-04' => 0,
                '2017-01-05' => 0,
                '2017-01-06' => 11,
                '2017-01-07' => 7,
                '2017-01-08' => 3,
                '2017-01-09' => 0,
                '2017-01-10' => 0
            ],
            'latestStreak' => 0,
            'latestStreakStart' => '',
            'latestStreakEnd' => '',
            'longestStreak' => 3,
            'longestStreakStart' => '2017-01-06',
            'longestStreakEnd' => '2017-01-08',
            'contributionsSum' => 21,
            'maxValue' => 11,
            'steps' => [
                8 => '#1e6823',
                6 => '#44a340',
                3 => '#8cc665',
                1 => '#d6e685'
            ]
        ]);
    }

    public function getCommitsMock() {
        $args = func_get_args();

        $file = __DIR__ . '/../data/bitbucket_commits_' . $args[0] . '_' . $args[1] . '.json';

        if (!file_exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    public function getIssuesMock() {
        $args = func_get_args();

        $file = __DIR__ . '/../data/bitbucket_issues_' . $args[0] . '_' . $args[1] . '.json';

        if (!file_exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    private function prepareTestGetContributions() {
        $this->bitBucketFetcher->method('getUser')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user1.json'));

        $this->bitBucketFetcher->method('getRepositories')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user1_repositories.json'));

        $bitBucketMock = $this->bitBucketMockBuilder->setConstructorArgs(
                [
                    [
                        'bitbucket_commit_usernames' => [
                            'testUsername'
                        ]
                    ],
                    $this->bitBucketFetcher
                ]
            )
            ->getMock(null);

        $this->gitHubFetcher->method('getCalendar')
            ->willReturn(file_get_contents(__DIR__ . '/../data/github_user1_contributions.html'));

        
        $gitHubMock = $this->gitHubMockBuilder->setConstructorArgs(
                [
                    [],
                    $this->gitHubFetcher
                ]
            )
            ->getMock(null);

        $factoryMock = $this->getMockBuilder('\GitBucketCalendar\Repositories\RepositoriesFactory')->getMock();

        $factoryMock->method('build')->willReturn([
            $gitHubMock,
            $bitBucketMock
        ]);

        $gitBucket = new GitBucketCalendar([
            'bitbucket_key' => '',
            'bitbucket_secret' => '',
            'bitbucket_commit_usernames' => [
                ''
            ],
            'github_account_username' => '',
            'memcached_host' => 'localhost',
            'memcached_port' => 11211
        ], $factoryMock);

        return $gitBucket;
    }

    private function prepareTestStreakWithoutCurrentDay() {
        $this->bitBucketFetcher->method('getUser')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user2.json'));

        $this->bitBucketFetcher->method('getRepositories')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user2_repositories.json'));

        $bitBucketMock = $this->bitBucketMockBuilder->setConstructorArgs(
                [
                    [
                        'bitbucket_commit_usernames' => [
                            'testUsername'
                        ]
                    ],
                    $this->bitBucketFetcher
                ]
            )
            ->getMock(null);

        $this->gitHubFetcher->method('getCalendar')
            ->willReturn(file_get_contents(__DIR__ . '/../data/github_user2_contributions.html'));

        
        $gitHubMock = $this->gitHubMockBuilder->setConstructorArgs(
                [
                    [],
                    $this->gitHubFetcher
                ]
            )
            ->getMock(null);

        $factoryMock = $this->getMockBuilder('\GitBucketCalendar\Repositories\RepositoriesFactory')->getMock();

        $factoryMock->method('build')->willReturn([
            $gitHubMock,
            $bitBucketMock
        ]);

        $gitBucket = new GitBucketCalendar([
            'bitbucket_key' => '',
            'bitbucket_secret' => '',
            'bitbucket_commit_usernames' => [
                ''
            ],
            'github_account_username' => '',
            'memcached_host' => 'localhost',
            'memcached_port' => 11211
        ], $factoryMock);

        return $gitBucket;
    }

    private function prepareTestBrokenStreak() {
        $this->bitBucketFetcher->method('getUser')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user3.json'));

        $this->bitBucketFetcher->method('getRepositories')
            ->willReturn(file_get_contents(__DIR__ . '/../data/bitbucket_user3_repositories.json'));

        $bitBucketMock = $this->bitBucketMockBuilder->setConstructorArgs(
                [
                    [
                        'bitbucket_commit_usernames' => [
                            'testUsername'
                        ]
                    ],
                    $this->bitBucketFetcher
                ]
            )
            ->getMock(null);

        $this->gitHubFetcher->method('getCalendar')
            ->willReturn(file_get_contents(__DIR__ . '/../data/github_user3_contributions.html'));

        
        $gitHubMock = $this->gitHubMockBuilder->setConstructorArgs(
                [
                    [],
                    $this->gitHubFetcher
                ]
            )
            ->getMock(null);

        $factoryMock = $this->getMockBuilder('\GitBucketCalendar\Repositories\RepositoriesFactory')->getMock();

        $factoryMock->method('build')->willReturn([
            $gitHubMock,
            $bitBucketMock
        ]);

        $gitBucket = new GitBucketCalendar([
            'bitbucket_key' => '',
            'bitbucket_secret' => '',
            'bitbucket_commit_usernames' => [
                ''
            ],
            'github_account_username' => '',
            'memcached_host' => 'localhost',
            'memcached_port' => 11211
        ], $factoryMock);

        return $gitBucket;
    }
}