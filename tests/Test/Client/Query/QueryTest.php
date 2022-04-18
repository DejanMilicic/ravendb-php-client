<?php

namespace tests\RavenDB\Test\Client\Query;

use RavenDB\Documents\DocumentStoreInterface;
use RavenDB\Documents\Queries\Query;
use RavenDB\Documents\Queries\SearchOperator;
use RavenDB\Documents\Session\BeforeQueryEventArgs;
use RavenDB\Documents\Session\DocumentQueryInterface;
use RavenDB\Exceptions\IllegalStateException;
use RavenDB\Utils\DateUtils;
use RavenDB\Utils\StringUtils;
use tests\RavenDB\Infrastructure\Entity\User;
use tests\RavenDB\Infrastructure\Entity\Order;
use tests\RavenDB\RemoteTestBase;
use tests\RavenDB\Test\Client\Query\Entity\Article;
use function PHPUnit\Framework\assertNotNull;

class QueryTest extends RemoteTestBase
{
    public function testCreateClausesForQueryDynamicallyWithOnBeforeQueryEvent()
    {
        $store = $this->getDocumentStore();

        try {
            $id1 = 'users/1';
            $id2 = 'users/2';

            $session = $store->openSession();

            try {
                $article1 = new Article();
                $article1->setTitle("foo");
                $article1->setDescription("bar");
                $article1->setDeleted(false);
                $session->store($article1, $id1);

                $article2 = new Article();
                $article2->setTitle("foo");
                $article2->setDescription("bar");
                $article2->setDeleted(true);
                $session->store($article2, $id2);

                $session->saveChanges();
            } finally {
                $session->close();
            }

            $session = $store->openSession();
            try {
                $session->advanced()->addBeforeQueryListener(function ($sender, BeforeQueryEventArgs $event) {
                    /** @var DocumentQueryInterface $queryToBeExecuted */
                    $queryToBeExecuted = $event->getQueryCustomization()->getQuery();
                    $queryToBeExecuted->andAlso(true);
                    $queryToBeExecuted->whereEquals("deleted", true);
                });

                $query = $session->query(Article::class)
                    ->search('title', 'foo')
                    ->search('description', 'bar', SearchOperator::or());

                $result = $query->toList();

                $this->assertEquals(
                    "from 'Articles' where (search(title, \$p0) or search(description, \$p1)) and deleted = \$p2",
                    $query->toString()
                );

                $this->assertCount(1, $result);

            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//  public void query_CreateClausesForQueryDynamicallyWhenTheQueryEmpty() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            String id1 = "users/1";
//            String id2 = "users/2";
//
//            try (IDocumentSession session = store.openSession()) {
//                Article article1 = new Article();
//                article1.setTitle("foo");
//                article1.setDescription("bar");
//                article1.setDeleted(false);
//                session.store(article1, id1);
//
//                Article article2 = new Article();
//                article2.setTitle("foo");
//                article2.setDescription("bar");
//                article2.setDeleted(true);
//                session.store(article2, id2);
//
//                session.saveChanges();
//            }
//
//            try (IDocumentSession session = store.openSession()) {
//                IDocumentQuery<Article> query = session.advanced().documentQuery(Article.class)
//                        .andAlso(true);
//
//                assertThat(query.toString())
//                        .isEqualTo("from 'Articles'");
//
//                List<Article> queryResult = query.toList();
//                assertThat(queryResult)
//                        .hasSize(2);
//            }
//        }
//    }

    public function testQuerySimple(): void
    {
        $store = $this->getDocumentStore();
        try {
            $session = $store->openSession();
            try {

                $user1 = new User();
                $user1->setName("John");

                $user2 = new User();
                $user2->setName("Jane");

                $user3 = new User();
                $user3->setName("Tarzan");

                $session->store($user1, "users/1");
                $session->store($user2, "users/2");
                $session->store($user3, "users/3");
                $session->saveChanges();

                $queryResult = $session
                    ->advanced()
                    ->documentQuery(User::class, null, "users", false)
                    ->toList();

                $this->assertCount(3, $queryResult);
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }
//  public void queryLazily() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            try (IDocumentSession session = store.openSession()) {
//
//                User user1 = new User();
//                user1.setName("John");
//
//                User user2 = new User();
//                user2.setName("Jane");
//
//                User user3 = new User();
//                user3.setName("Tarzan");
//
//                session.store(user1, "users/1");
//                session.store(user2, "users/2");
//                session.store(user3, "users/3");
//                session.saveChanges();
//
//                Lazy<List<User>> lazyQuery = session.query(User.class)
//                        .lazily();
//
//                List<User> queryResult = lazyQuery.getValue();
//
//                assertThat(queryResult)
//                        .hasSize(3);
//
//                assertThat(queryResult.get(0).getName())
//                        .isEqualTo("John");
//            }
//        }
//    }
//
//    @Test
//    public void collectionsStats() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            try (IDocumentSession session = store.openSession()) {
//
//                User user1 = new User();
//                user1.setName("John");
//
//                User user2 = new User();
//                user2.setName("Jane");
//
//                session.store(user1, "users/1");
//                session.store(user2, "users/2");
//                session.saveChanges();
//            }
//
//            CollectionStatistics stats = store.maintenance().send(new GetCollectionStatisticsOperation());
//
//            assertThat(stats.getCountOfDocuments())
//                    .isEqualTo(2);
//
//            assertThat(stats.getCollections().get("Users"))
//                    .isEqualTo(2);
//        }
//    }

    public function testQueryWithWhereClause(): void
    {
        $store = $this->getDocumentStore();

        try {
            $session = $store->openSession();
            try {
                $user1 = new User();
                $user1->setName("John");

                $user2 = new User();
                $user2->setName("Jane");

                $user3 = new User();
                $user3->setName("Tarzan");

                $session->store($user1, "users/1");
                $session->store($user2, "users/2");
                $session->store($user3, "users/3");
                $session->saveChanges();

                $queryResult = $session->query(User::class, Query::collection("users"))
                    ->whereStartsWith("name", "J")
                    ->toList();

                $queryResult2 = $session->query(User::class, Query::collection("users"))
                    ->whereEquals("name", "Tarzan")
                    ->toList();

                $queryResult3 = $session->query(User::class, Query::collection("users"))
                    ->whereEndsWith("name", "n")
                    ->toList();

                $this->assertCount(2, $queryResult);

                $this->assertCount(1, $queryResult2);

                $this->assertCount(2, $queryResult3);

            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//    @Test
//    public void queryMapReduceWithCount() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//                List<ReduceResult> results = session.query(User.class)
//                        .groupBy("name")
//                        .selectKey()
//                        .selectCount()
//                        .orderByDescending("count")
//                        .ofType(ReduceResult.class)
//                        .toList();
//
//                assertThat(results.get(0).getCount())
//                        .isEqualTo(2);
//                assertThat(results.get(0).getName())
//                        .isEqualTo("John");
//
//                assertThat(results.get(1).getCount())
//                        .isEqualTo(1);
//                assertThat(results.get(1).getName())
//                        .isEqualTo("Tarzan");
//            }
//        }
//    }
//
//    @Test
//    public void queryMapReduceWithSum() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//                List<ReduceResult> results = session.query(User.class)
//                        .groupBy("name")
//                        .selectKey()
//                        .selectSum(new GroupByField("age"))
//                        .orderByDescending("age")
//                        .ofType(ReduceResult.class)
//                        .toList();
//
//                assertThat(results.get(0).getAge())
//                        .isEqualTo(8);
//                assertThat(results.get(0).getName())
//                        .isEqualTo("John");
//
//                assertThat(results.get(1).getAge())
//                        .isEqualTo(2);
//                assertThat(results.get(1).getName())
//                        .isEqualTo("Tarzan");
//            }
//        }
//    }
//
//    @Test
//    public void queryMapReduceIndex() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<ReduceResult> results = session.query(ReduceResult.class, Query.index("UsersByName"))
//                        .orderByDescending("count")
//                        .toList();
//
//                assertThat(results.get(0).getCount())
//                        .isEqualTo(2);
//                assertThat(results.get(0).getName())
//                        .isEqualTo("John");
//
//                assertThat(results.get(1).getCount())
//                        .isEqualTo(1);
//                assertThat(results.get(1).getName())
//                        .isEqualTo("Tarzan");
//            }
//        }
//    }
//
//    @Test
//    public void querySingleProperty() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<Integer> ages = session.query(User.class)
//                        .addOrder("age", true, OrderingType.LONG)
//                        .selectFields(Integer.class, "age")
//                        .toList();
//
//                assertThat(ages)
//                        .hasSize(3)
//                        .containsSequence(5, 3, 2);
//            }
//        }
//    }
//
//    @Test
//    public void queryWithSelect() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<User> usersAge = session.query(User.class)
//                        .selectFields(User.class, "age", "id")
//                        .toList();
//
//                for (User user : usersAge) {
//                    assertThat(user.getAge())
//                            .isPositive();
//
//                    assertThat(user.getId())
//                            .isNotNull();
//                }
//            }
//        }
//    }
//
//    @Test
//    public void queryWithWhereIn() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<User> users = session.query(User.class)
//                        .whereIn("name", Arrays.asList("Tarzan", "no_such"))
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(1);
//            }
//        }
//    }

    public function testQueryWithWhereBetween(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {

                $users = $session->query(User::class)
                    ->whereBetween("age", 4, 5)
                    ->toList();

                $this->assertCount(1, $users);

                $this->assertEquals("John", $users[0]->getName());

            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//    @Test
//    public void queryWithWhereLessThan() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<User> users = session.query(User.class)
//                        .whereLessThan("age", 3)
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(1);
//
//                assertThat(users.get(0).getName())
//                        .isEqualTo("Tarzan");
//
//            }
//        }
//    }
//
//    @Test
//    public void queryWithWhereLessThanOrEqual() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<User> users = session.query(User.class)
//                        .whereLessThanOrEqual("age", 3)
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(2);
//
//            }
//        }
//    }

    public function testQueryWithWhereGreaterThan(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {

                $users = $session->query(User::class)
                    ->whereGreaterThan("age", 3)
                    ->toList();

                $this->assertCount(1, $users);

                $this->assertEquals("John", $users[0]->getName());

            } finally {
                $session->close();
            }
        } finally {
            $store->close();;
        }
    }

//    @Test
//    public void queryWithWhereGreaterThanOrEqual() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//                List<User> users = session.query(User.class)
//                        .whereGreaterThanOrEqual("age", 3)
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(2);
//
//            }
//        }
//    }
//
//  private static class UserProjection {
//        private String id;
//        private String name;
//
//        public String getId() {
//            return id;
//        }
//
//        public void setId(String id) {
//            this.id = id;
//        }
//
//        public String getName() {
//            return name;
//        }
//
//        public void setName(String name) {
//            this.name = name;
//        }
//    }
//
//  @Test
//    public void queryWithProjection() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<UserProjection> projections = session.query(User.class)
//                        .selectFields(UserProjection.class)
//                        .toList();
//
//                assertThat(projections)
//                        .hasSize(3);
//
//                for (UserProjection projection : projections) {
//                    assertThat(projection.getId())
//                            .isNotNull();
//
//                    assertThat(projection.getName())
//                            .isNotNull();
//                }
//            }
//        }
//    }
//
//    @Test
//    public void queryWithProjection2() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//
//                List<UserProjection> projections = session.query(User.class)
//                        .selectFields(UserProjection.class, "lastName", "id")
//                        .toList();
//
//                assertThat(projections)
//                        .hasSize(3);
//
//                for (UserProjection projection : projections) {
//                    assertThat(projection.getId())
//                            .isNotNull();
//
//                    assertThat(projection.getName())
//                            .isNull(); // we didn't specify this field in mapping
//                }
//            }
//        }
//    }
//
//    @Test
//    public void queryDistinct() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (IDocumentSession session = store.openSession()) {
//                List<String> uniqueNames = session.query(User.class)
//                        .selectFields(String.class, "name")
//                        .distinct()
//                        .toList();
//
//                assertThat(uniqueNames)
//                        .hasSize(2)
//                        .contains("Tarzan")
//                        .contains("John");
//            }
//        }
//    }


    public function testQuerySearchWithOr(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $uniqueNames = $session->query(User::class)
                    ->search("name", "Tarzan John", SearchOperator::or())
                    ->toList();

                $this->assertCount(3, $uniqueNames);
            } finally {
                $session->close();
            }

        } finally {
            $store->close();
        }
    }
//
//    @Test
//    public void queryNoTracking() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (DocumentSession session = (DocumentSession) store.openSession()) {
//                List<User> users = session.query(User.class)
//                        .noTracking()
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(3);
//
//                for (User user : users) {
//                    assertThat(session.isLoaded(user.getId()))
//                            .isFalse();
//                }
//            }
//        }
//    }
//

    public function testQuerySkipTake(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $users = $session->query(User::class)
                    ->orderBy("name")
                    ->skip(2)
                    ->take(1)
                    ->toList();

                $this->assertCount(1, $users);

                $this->assertEquals("Tarzan", $users[0]->getName());

            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

    public function testRawQuerySkipTake(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $users = $session->rawQuery(User::class, "from users")
                    ->skip(2)
                    ->take(1)
                    ->toList();

                $this->assertCount(1, $users);

                $this->assertEquals("Tarzan", $users[0]->getName());
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//    @Test
//    public void parametersInRawQuery() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (DocumentSession session = (DocumentSession) store.openSession()) {
//                List<User> users = session.rawQuery(User.class, "from users where age == $p0")
//                        .addParameter("p0", 5)
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(1);
//
//                assertThat(users.get(0).getName())
//                        .isEqualTo("John");
//            }
//        }
//    }

    public function testQueryLucene(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $users = $session->query(User::class)
                    ->whereLucene("name", "Tarzan")
                    ->toList();

                $this->assertCount(1, $users);

                foreach ($users as $user) {
                    $this->assertEquals("Tarzan", $user->getName());
                }
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

    public function testQueryWhereExact(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $users = $session->query(User::class)
                    ->whereEquals("name", "tarzan")
                    ->toList();

                $this->assertCount(1, $users);

                $users = $session->query(User::class)
                    ->whereEquals("name", "tarzan", true)
                    ->toList();

                $this->assertCount(0, $users); // we queried for tarzan with exact

                $users = $session->query(User::class)
                    ->whereEquals("name", "Tarzan", true)
                    ->toList();

                $this->assertCount(1, $users); // we queried for Tarzan with exact
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

    public function testQueryWhereNot(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {
                $this->assertCount(
                    2,
                    $session->query(User::class)
                        ->not()
                        ->whereEquals("name", "tarzan")
                        ->toList()
                );

                $this->assertCount(
                    2,
                    $session->query(User::class)
                        ->whereNotEquals("name", "tarzan")
                        ->toList()
                );

                $this->assertCount(
                    2,
                    $session->query(User::class)
                        ->whereNotEquals("name", "Tarzan", true)
                        ->toList()
                );
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//    public static class OrderTime extends AbstractIndexCreationTask {
//        public static class Result {
//            private long delay;
//
//            public long getDelay() {
//                return delay;
//            }
//
//            public void setDelay(long delay) {
//                this.delay = delay;
//            }
//        }
//
//        public OrderTime() {
//            map = "from order in docs.Orders " +
//                    "select new { " +
//                    "  delay = order.shippedAt - ((DateTime?)order.orderedAt) " +
//                    "}";
//        }
//    }

    /** @todo uncoment all commented code in this test */
    public function atestQueryWithDuration(): void
    {
        $store = $this->getDocumentStore();
        try {
            $now = new \DateTime();

//            $store->executeIndex(new OrderTime());

            $session = $store->openSession();
            try {
                $order1 = new Order();
                $order1->setCompany("hours");
                $order1->setOrderedAt(DateUtils::addHours($now, -2));
                $order1->setShippedAt($now);
                $session->store($order1);

                $order2 = new Order();
                $order2->setCompany("days");
                $order2->setOrderedAt(DateUtils::addDays($now, -2));
                $order2->setShippedAt($now);
                $session->store($order2);

                $order3 = new Order();
                $order3->setCompany("minutes");
                $order3->setOrderedAt(DateUtils::addMinutes($now, -2));
                $order3->setShippedAt($now);
                $session->store($order3);

                $session->saveChanges();
            } finally {
                $session->close();
            }

//            $this->waitForIndexing($store);

            $session = $store->openSession();
            try {
//                Set<String> delay = session.query(Order.class, OrderTime.class)
//                        .whereLessThan("delay", Duration.ofHours(3))
//                        .toList()
//                        .stream()
//                        .map(x -> x.getCompany())
//                        .collect(toSet());
//
//                assertThat(delay)
//                        .containsExactly("hours", "minutes");
//
//                Set<String> delay2 = session.query(Order.class, OrderTime.class)
//                        .whereGreaterThan("delay", Duration.ofHours(3))
//                        .toList()
//                        .stream()
//                        .map(x -> x.getCompany())
//                        .collect(toSet());
//
//                assertThat(delay2)
//                        .containsExactly("days");
//
//
            } finally {
                $session->close();
            }

        } finally {
            $store->close();
        }
    }

    public function testQueryFirst(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();

            try {
                $first = $session->query(User::class)
                    ->first();

                $this->assertNotNull($first);

                $this->assertNotNull(
                    $session->query(User::class)
                        ->whereEquals("name", "Tarzan")
                        ->single()
                );

                $this->assertNotNull($first);

                $this->expectException(IllegalStateException::class);
                $session->query(User::class)
                    ->single();
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }

    }
//
//    @Test
//    public void queryParameters() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (DocumentSession session = (DocumentSession) store.openSession()) {
//
//                assertThat(session.rawQuery(User.class, "from Users where name = $name")
//                        .addParameter("name", "Tarzan")
//                        .count())
//                        .isEqualTo(1);
//            }
//        }
//    }

    public function testQueryRandomOrder(): void
    {
        $store = $this->getDocumentStore();
        try {
            $this->addUsers($store);

            $session = $store->openSession();
            try {

                $this->assertCount(3, $session->query(User::class)
                    ->randomOrdering()
                    ->toList());

                $this->assertCount(3, $session->query(User::class)
                    ->randomOrdering("123")
                    ->toList());
            } finally {
                $session->close();
            }
        } finally {
            $store->close();
        }
    }

//    @Test
//    public void queryWhereExists() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (DocumentSession session = (DocumentSession) store.openSession()) {
//                assertThat(session.query(User.class)
//                        .whereExists("name")
//                        .toList())
//                        .hasSize(3);
//
//                assertThat(session.query(User.class)
//                        .whereExists("name")
//                        .andAlso()
//                        .not()
//                        .whereExists("no_such_field")
//                        .toList())
//                        .hasSize(3);
//            }
//        }
//    }
//
//    @Test
//    public void queryWithBoost() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//            addUsers(store);
//
//            try (DocumentSession session = (DocumentSession) store.openSession()) {
//                List<User> users = session.query(User.class)
//                        .whereEquals("name", "Tarzan")
//                        .boost(5)
//                        .orElse()
//                        .whereEquals("name", "John")
//                        .boost(2)
//                        .orderByScore()
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(3);
//
//                List<String> names = users.stream().map(User::getName).collect(toList());
//                assertThat(names)
//                        .containsSequence("Tarzan", "John", "John");
//
//                users = session.query(User.class)
//                        .whereEquals("name", "Tarzan")
//                        .boost(2)
//                        .orElse()
//                        .whereEquals("name", "John")
//                        .boost(5)
//                        .orderByScore()
//                        .toList();
//
//                assertThat(users)
//                        .hasSize(3);
//
//                names = users.stream().map(x -> x.getName()).collect(toList());
//                assertThat(names)
//                        .containsSequence("John", "John", "Tarzan");
//            }
//        }
//    }
//
//    public static class UsersByName extends AbstractIndexCreationTask {
//        public UsersByName() {
//
//            map = "from c in docs.Users select new " +
//                    " {" +
//                    "    c.name, " +
//                    "    count = 1" +
//                    "}";
//
//            reduce = "from result in results " +
//                    "group result by result.name " +
//                    "into g " +
//                    "select new " +
//                    "{ " +
//                    "  name = g.Key, " +
//                    "  count = g.Sum(x => x.count) " +
//                    "}";
//        }
//    }
//
    private function addUsers(DocumentStoreInterface $store): void
    {
        $session = $store->openSession();
        try {
            $user1 = new User();
            $user1->setName("John");
            $user1->setAge(3);

            $user2 = new User();
            $user2->setName("John");
            $user2->setAge(5);

            $user3 = new User();
            $user3->setName("Tarzan");
            $user3->setAge(2);

            $session->store($user1, "users/1");
            $session->store($user2, "users/2");
            $session->store($user3, "users/3");
            $session->saveChanges();
        } finally {
            $session->close();
        }

//        $store->executeIndex(new UsersByName());
//        $this->waitForIndexing(store);
    }

//    public static class ReduceResult {
//        private int count;
//        private String name;
//        private int age;
//
//        public int getAge() {
//            return age;
//        }
//
//        public void setAge(int age) {
//            this.age = age;
//        }
//
//        public int getCount() {
//            return count;
//        }
//
//        public void setCount(int count) {
//            this.count = count;
//        }
//
//        public String getName() {
//            return name;
//        }
//
//        public void setName(String name) {
//            this.name = name;
//        }
//    }
//
//    @Test
//    public void queryWithCustomize() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//
//            new DogsIndex().execute(store);
//
//            try (IDocumentSession newSession = store.openSession()) {
//                createDogs(newSession);
//
//                newSession.saveChanges();
//            }
//
//            try (IDocumentSession newSession = store.openSession()) {
//
//                List<DogsIndex.Result> queryResult = newSession.advanced()
//                        .documentQuery(DogsIndex.Result.class, new DogsIndex().getIndexName(), null, false)
//                        .waitForNonStaleResults(null)
//                        .orderBy("name", OrderingType.ALPHA_NUMERIC)
//                        .whereGreaterThan("age", 2)
//                        .toList();
//
//                assertThat(queryResult)
//                        .hasSize(4);
//
//                assertThat(queryResult.get(0).getName())
//                        .isEqualTo("Brian");
//
//                assertThat(queryResult.get(1).getName())
//                        .isEqualTo("Django");
//
//                assertThat(queryResult.get(2).getName())
//                        .isEqualTo("Lassie");
//
//                assertThat(queryResult.get(3).getName())
//                        .isEqualTo("Snoopy");
//            }
//        }
//    }
//
//    private void createDogs(IDocumentSession newSession) {
//        Dog dog1 = new Dog();
//        dog1.setName("Snoopy");
//        dog1.setBreed("Beagle");
//        dog1.setColor("White");
//        dog1.setAge(6);
//        dog1.setVaccinated(true);
//
//        newSession.store(dog1, "docs/1");
//
//        Dog dog2 = new Dog();
//        dog2.setName("Brian");
//        dog2.setBreed("Labrador");
//        dog2.setColor("White");
//        dog2.setAge(12);
//        dog2.setVaccinated(false);
//
//        newSession.store(dog2, "docs/2");
//
//        Dog dog3 = new Dog();
//        dog3.setName("Django");
//        dog3.setBreed("Jack Russel");
//        dog3.setColor("Black");
//        dog3.setAge(3);
//        dog3.setVaccinated(true);
//
//        newSession.store(dog3, "docs/3");
//
//        Dog dog4 = new Dog();
//        dog4.setName("Beethoven");
//        dog4.setBreed("St. Bernard");
//        dog4.setColor("Brown");
//        dog4.setAge(1);
//        dog4.setVaccinated(false);
//
//        newSession.store(dog4, "docs/4");
//
//        Dog dog5 = new Dog();
//        dog5.setName("Scooby Doo");
//        dog5.setBreed("Great Dane");
//        dog5.setColor("Brown");
//        dog5.setAge(0);
//        dog5.setVaccinated(false);
//
//        newSession.store(dog5, "docs/5");
//
//        Dog dog6 = new Dog();
//        dog6.setName("Old Yeller");
//        dog6.setBreed("Black Mouth Cur");
//        dog6.setColor("White");
//        dog6.setAge(2);
//        dog6.setVaccinated(true);
//
//        newSession.store(dog6, "docs/6");
//
//        Dog dog7 = new Dog();
//        dog7.setName("Benji");
//        dog7.setBreed("Mixed");
//        dog7.setColor("White");
//        dog7.setAge(0);
//        dog7.setVaccinated(false);
//
//        newSession.store(dog7, "docs/7");
//
//        Dog dog8 = new Dog();
//        dog8.setName("Lassie");
//        dog8.setBreed("Collie");
//        dog8.setColor("Brown");
//        dog8.setAge(6);
//        dog8.setVaccinated(true);
//
//        newSession.store(dog8, "docs/8");
//    }

    public function testQueryLongRequest(): void
    {
        $store = $this->getDocumentStore();
        try {
            $newSession = $store->openSession();
            try {
                $longName = StringUtils::repeat('x', 2048);
                $user = new User();
                $user->setName($longName);
                $newSession->store($user, "users/1");

                $newSession->saveChanges();

                $queryResult = $newSession
                    ->advanced()
                    ->documentQuery(User::class, null, "Users", false)
                    ->whereEquals("name", $longName)
                    ->toList();

                $this->assertCount(1, $queryResult);
            } finally {
                $newSession->close();
            }
        } finally {
            $store->close();
        }
    }

//    @Test
//    public void queryByIndex() throws Exception {
//        try (IDocumentStore store = getDocumentStore()) {
//
//            new DogsIndex().execute(store);
//
//            try (IDocumentSession newSession = store.openSession()) {
//                createDogs(newSession);
//
//                newSession.saveChanges();
//
//                waitForIndexing(store, store.getDatabase(), null);
//            }
//
//            try (IDocumentSession newSession = store.openSession()) {
//                List<DogsIndex.Result> queryResult = newSession.advanced()
//                        .documentQuery(DogsIndex.Result.class, new DogsIndex().getIndexName(), null, false)
//                        .whereGreaterThan("age", 2)
//                        .andAlso()
//                        .whereEquals("vaccinated", false)
//                        .toList();
//
//                assertThat(queryResult)
//                        .hasSize(1);
//
//                assertThat(queryResult.get(0).getName())
//                        .isEqualTo("Brian");
//
//
//                List<DogsIndex.Result> queryResult2 = newSession.advanced()
//                        .documentQuery(DogsIndex.Result.class, new DogsIndex().getIndexName(), null, false)
//                        .whereLessThanOrEqual("age", 2)
//                        .andAlso()
//                        .whereEquals("vaccinated", false)
//                        .toList();
//
//                assertThat(queryResult2)
//                        .hasSize(3);
//
//                List<String> list = queryResult2.stream()
//                        .map(x -> x.getName())
//                        .collect(toList());
//
//                assertThat(list)
//                        .contains("Beethoven")
//                        .contains("Scooby Doo")
//                        .contains("Benji");
//            }
//        }
//    }
//
//    public static class Dog {
//        private String id;
//        private String name;
//        private String breed;
//        private String color;
//        private int age;
//        private boolean isVaccinated;
//
//        public String getId() {
//            return id;
//        }
//
//        public void setId(String id) {
//            this.id = id;
//        }
//
//        public String getName() {
//            return name;
//        }
//
//        public void setName(String name) {
//            this.name = name;
//        }
//
//        public String getBreed() {
//            return breed;
//        }
//
//        public void setBreed(String breed) {
//            this.breed = breed;
//        }
//
//        public String getColor() {
//            return color;
//        }
//
//        public void setColor(String color) {
//            this.color = color;
//        }
//
//        public int getAge() {
//            return age;
//        }
//
//        public void setAge(int age) {
//            this.age = age;
//        }
//
//        public boolean isVaccinated() {
//            return isVaccinated;
//        }
//
//        public void setVaccinated(boolean vaccinated) {
//            isVaccinated = vaccinated;
//        }
//    }
//
//    public static class DogsIndex extends AbstractIndexCreationTask {
//        public static class Result {
//            private String name;
//            private int age;
//            private boolean isVaccinated;
//
//            public String getName() {
//                return name;
//            }
//
//            public void setName(String name) {
//                this.name = name;
//            }
//
//            public int getAge() {
//                return age;
//            }
//
//            public void setAge(int age) {
//                this.age = age;
//            }
//
//            public boolean isVaccinated() {
//                return isVaccinated;
//            }
//
//            public void setVaccinated(boolean vaccinated) {
//                isVaccinated = vaccinated;
//            }
//        }
//
//        public DogsIndex() {
//            map = "from dog in docs.dogs select new { dog.name, dog.age, dog.vaccinated }";
//        }
//    }
}
