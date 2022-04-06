<?php

namespace tests\RavenDB\Driver;

use PHPUnit\Framework\TestCase;
use RavenDB\Documents\DocumentStore;
use RavenDB\Documents\DocumentStoreInterface;
use RavenDB\Exceptions\IllegalStateException;
use RavenDB\Http\Adapter\HttpClient;
use RavenDB\Type\Url;
use RavenDB\Type\UrlArray;

abstract class RavenTestDriver extends TestCase
{
    protected bool $disposed = false;

    public function isDisposed(): bool
    {
        return $this->disposed;
    }

    public static bool $debug = false;

//  @todo: implement method
    protected function hookLeakedConnectionCheck(DocumentStore $store): void
    {

    }
//    protected void hookLeakedConnectionCheck(DocumentStore store) {
//        store.addBeforeCloseListener((sender, event) -> {
//            try {
//                CloseableHttpClient httpClient = store.getRequestExecutor().getHttpClient();
//
//                Field connManager = httpClient.getClass().getDeclaredField("connManager");
//                connManager.setAccessible(true);
//                PoolingHttpClientConnectionManager connectionManager = (PoolingHttpClientConnectionManager) connManager.get(httpClient);
//
//                int leased = connectionManager.getTotalStats().getLeased();
//                if (leased > 0) {
//                    Thread.sleep(500);
//
//                    // give another try
//                    leased = connectionManager.getTotalStats().getLeased();
//
//                    if (leased > 0) {
//                        throw new IllegalStateException("Looks like you have leaked " + leased + " connections!");
//                    }
//
//                    /*  debug code to find actual connections
//                    Field poolField = connectionManager.getClass().getDeclaredField("pool");
//                    poolField.setAccessible(true);
//                    AbstractConnPool pool = (AbstractConnPool) poolField.get(connectionManager);
//                    Field leasedField = pool.getClass().getSuperclass().getDeclaredField("leased");
//                    leasedField.setAccessible(true);
//                    Set leasedConnections = (Set) leasedField.get(pool);*/
//                }
//            } catch (NoSuchFieldException | IllegalAccessException | InterruptedException e) {
//                throw new IllegalStateException("Unable to check for leaked connections", e);
//            }
//        });
//    }
//
//    protected static void reportError(Exception e) {
//        if (!debug) {
//            return;
//        }
//
//        if (e == null) {
//            throw new IllegalArgumentException("Exception can not be null");
//        }
//    }
//

    protected function reportInfo(string $message): void
    {

    }

    public function withFiddler(): void
    {
        HttpClient::useProxy('http://127.0.0.1:8866');
    }

    protected function setupDatabase(DocumentStoreInterface $documentStore): void
    {
        // empty by design
    }

    protected function runServerInternal(RavenServerLocator $locator, Callable $configureStore = null): DocumentStoreInterface
    {
        $this->reportInfo('Starting global server');

        $urls = new UrlArray();

        $arguments = $locator->getCommandArguments();
        $prefix = '--ServerUrl=';
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, $prefix)) {
                $urls->append(new Url(substr($argument, strlen($prefix))));
            }
        }

        if (count($urls) == 0) {
            $this->reportInfo('Url is null');
            throw new IllegalStateException('Unable to start server');
        }

        $store = new DocumentStore('test.manager');
        $store->setUrls($urls);
        $store->getConventions()->setDisableTopologyUpdates(true);

        if ($configureStore != null) {
            $configureStore($store);
        }

        $store->initialize();

        return $store;
    }
//    protected IDocumentStore runServerInternal(RavenServerLocator locator, Reference<Process> processReference, Consumer<DocumentStore> configureStore) throws Exception {
//        Process process = RavenServerRunner.run(locator);
//        processReference.value = process;
//
//        reportInfo("Starting global server");
//
//        String url = null;
//        InputStream stdout = process.getInputStream();
//
//        Stopwatch startupDuration = Stopwatch.createStarted();
//        BufferedReader reader = new BufferedReader(new InputStreamReader(stdout));
//
//        List<String> readLines = new ArrayList<>();
//
//        while (true) {
//            String line = reader.readLine();
//            readLines.add(line);
//
//            if (line == null) {
//                throw new RuntimeException(readLines
//                        .stream()
//                        .collect(Collectors.joining(System.lineSeparator())) + IOUtils.toString(process.getInputStream(), StandardCharsets.UTF_8));
//            }
//
//            if (startupDuration.elapsed(TimeUnit.MINUTES) >= 1) {
//                break;
//            }
//
//            String prefix = "Server available on: ";
//            if (line.startsWith(prefix)) {
//                url = line.substring(prefix.length());
//                break;
//            }
//        }
//
//        if (url == null) {
//            reportInfo("Url is null");
//
//            try {
//                process.destroyForcibly();
//            } catch (Exception e) {
//                reportError(e);
//            }
//
//            throw new IllegalStateException("Unable to start server");
//        }
//
//        DocumentStore store = new DocumentStore();
//        store.setUrls(new String[] { url });
//        store.setDatabase("test.manager");
//        store.getConventions().setDisableTopologyUpdates(true);
//
//        if (configureStore != null) {
//            configureStore.accept(store);
//        }
//
//        return store.initialize();
//    }

// @todo: implement method
    public static function waitForIndexing(
        DocumentStoreInterface $store,
        ?string $database = null,
        ?\DateInterval $timeout = null,
        ?string $nodeTag = null
    ): void {

    }

//    public static void waitForIndexing(IDocumentStore store, String database, Duration timeout, String nodeTag) {
//        MaintenanceOperationExecutor admin = store.maintenance().forDatabase(database);
//
//        if (timeout == null) {
//            timeout = Duration.ofMinutes(1);
//        }
//
//        Stopwatch sp = Stopwatch.createStarted();
//
//        while (sp.elapsed(TimeUnit.MILLISECONDS) < timeout.toMillis()) {
//            DatabaseStatistics databaseStatistics = admin.send(new GetStatisticsOperation("wait-for-indexing", nodeTag));
//
//            List<IndexInformation> indexes = Arrays.stream(databaseStatistics.getIndexes())
//                    .filter(x -> !IndexState.DISABLED.equals(x.getState()))
//                    .collect(Collectors.toList());
//
//            if (indexes.stream().allMatch(x -> !x.isStale() &&
//                    !x.getName().startsWith(Constants.Documents.Indexing.SIDE_BY_SIDE_INDEX_NAME_PREFIX))) {
//                return;
//            }
//
//            if (Arrays.stream(databaseStatistics.getIndexes()).anyMatch(x -> IndexState.ERROR.equals(x.getState()))) {
//                break;
//            }
//
//            try {
//                Thread.sleep(100);
//            } catch (InterruptedException e) {
//                throw new RuntimeException(e);
//            }
//        }
//
//
//        IndexErrors[] errors = admin.send(new GetIndexErrorsOperation());
//        String allIndexErrorsText = "";
//        Function<IndexErrors, String> formatIndexErrors = indexErrors -> {
//            String errorsListText = Arrays.stream(indexErrors.getErrors()).map(x -> "-" + x).collect(Collectors.joining(System.lineSeparator()));
//            return "Index " + indexErrors.getName() + " (" + indexErrors.getErrors().length + " errors): "+ System.lineSeparator() + errorsListText;
//        };
//        if (errors != null && errors.length > 0) {
//            allIndexErrorsText = Arrays.stream(errors).map(formatIndexErrors).collect(Collectors.joining(System.lineSeparator()));
//        }
//
//        throw new TimeoutException("The indexes stayed stale for more than " + timeout + "." + allIndexErrorsText);
//    }
//
//    public static IndexErrors[] waitForIndexingErrors(IDocumentStore store, Duration timeout, String... indexNames) throws InterruptedException {
//        Stopwatch sw = Stopwatch.createStarted();
//
//        while (sw.elapsed().compareTo(timeout) < 0) {
//            IndexErrors[] indexes = store.maintenance().send(new GetIndexErrorsOperation(indexNames));
//
//            for (IndexErrors index : indexes) {
//                if (index.getErrors() != null && index.getErrors().length > 0) {
//                    return indexes;
//                }
//            }
//
//            Thread.sleep(32);
//        }
//
//        throw new TimeoutException("Got no index error for more than " + timeout.toString());
//    }
//
//    protected boolean waitForDocumentDeletion(IDocumentStore store, String id) throws InterruptedException {
//        Stopwatch sw = Stopwatch.createStarted();
//
//        while (sw.elapsed(TimeUnit.MILLISECONDS) <= 10_000) {
//            try (IDocumentSession session = store.openSession()) {
//                if (!session.advanced().exists(id)) {
//                    return true;
//                }
//            }
//
//            Thread.sleep(100);
//        }
//
//        return false;
//    }
//
//    protected static <T> T waitForValue(Supplier<T> act, T expectedValue) throws InterruptedException {
//        return waitForValue(act, expectedValue, Duration.ofSeconds(15));
//    }
//
//    protected static <T> T waitForValue(Supplier<T> act, T expectedValue, Duration timeout) throws InterruptedException {
//        Stopwatch sw = Stopwatch.createStarted();
//
//        do {
//            try {
//                T currentVal = act.get();
//                if (expectedValue.equals(currentVal)) {
//                    return currentVal;
//                }
//
//                if (sw.elapsed().compareTo(timeout) > 0) {
//                    return currentVal;
//                }
//            } catch (Exception e) {
//                if (sw.elapsed().compareTo(timeout) > 0) {
//                    throw new RuntimeException(e);
//                }
//            }
//
//            Thread.sleep(16);
//        } while (true);
//    }
//
//    protected static void killProcess(Process p) {
//        if (p != null && p.isAlive()) {
//            reportInfo("Kill global server");
//
//            try {
//                p.destroyForcibly();
//            } catch (Exception e) {
//                reportError(e);
//            }
//        }
//    }
//
//    public void waitForUserToContinueTheTest(IDocumentStore store) {
//        String databaseNameEncoded = UrlUtils.escapeDataString(store.getDatabase());
//        String documentsPage = store.getUrls()[0] + "/studio/index.html#databases/documents?&database=" + databaseNameEncoded + "&withStop=true&disableAnalytics=true";
//
//        openBrowser(documentsPage);
//
//        do {
//            try {
//                Thread.sleep(500);
//            } catch (InterruptedException ignored) {
//            }
//
//            try (IDocumentSession session = store.openSession()) {
//                if (session.load(ObjectNode.class, "Debug/Done") != null) {
//                    break;
//                }
//            }
//
//        } while (true);
//    }
//
//    protected void openBrowser(String url) {
//        System.out.println(url);
//
//        if (Desktop.isDesktopSupported()) {
//            Desktop desktop = Desktop.getDesktop();
//            try {
//                desktop.browse(new URI(url));
//            } catch (IOException | URISyntaxException e) {
//                throw new RuntimeException(e);
//            }
//        } else {
//            Runtime runtime = Runtime.getRuntime();
//            try {
//                runtime.exec("xdg-open " + url);
//            } catch (IOException e) {
//                throw new RuntimeException(e);
//            }
//        }
//    }
//
//    @SuppressWarnings("UnusedReturnValue")
//    protected static ConfigureRevisionsOperation.ConfigureRevisionsOperationResult setupRevisions(IDocumentStore store, boolean purgeOnDelete, long minimumRevisionsToKeep) {
//        RevisionsConfiguration revisionsConfiguration = new RevisionsConfiguration();
//        RevisionsCollectionConfiguration defaultCollection = new RevisionsCollectionConfiguration();
//        defaultCollection.setPurgeOnDelete(purgeOnDelete);
//        defaultCollection.setMinimumRevisionsToKeep(minimumRevisionsToKeep);
//
//        revisionsConfiguration.setDefaultConfig(defaultCollection);
//        ConfigureRevisionsOperation operation = new ConfigureRevisionsOperation(revisionsConfiguration);
//
//        return store.maintenance().send(operation);
//    }
//
//    protected static void createSimpleData(IDocumentStore store) {
//        try (IDocumentSession session = store.openSession()) {
//            Entity entityA = new Entity();
//            entityA.setId("entity/1");
//            entityA.setName("A");
//
//            Entity entityB = new Entity();
//            entityB.setId("entity/2");
//            entityB.setName("B");
//
//            Entity entityC = new Entity();
//            entityC.setId("entity/3");
//            entityC.setName("C");
//
//            session.store(entityA);
//            session.store(entityB);
//            session.store(entityC);
//
//            entityA.setReferences(entityB.getId());
//            entityB.setReferences(entityC.getId());
//            entityC.setReferences(entityA.getId());
//
//            session.saveChanges();
//        }
//    }
//
//    protected static void createDogDataWithoutEdges(IDocumentStore store) {
//        try (IDocumentSession session = store.openSession()) {
//            Dog arava = new Dog();
//            arava.setName("Arava");
//
//            Dog oscar = new Dog();
//            oscar.setName("Oscar");
//
//            Dog pheobe = new Dog();
//            pheobe.setName("Pheobe");
//
//            session.store(arava);
//            session.store(oscar);
//            session.store(pheobe);
//
//            session.saveChanges();
//        }
//    }
//
//    protected static void createDataWithMultipleEdgesOfTheSameType(IDocumentStore store) {
//        try (IDocumentSession session = store.openSession()) {
//            Dog arava = new Dog();
//            arava.setName("Arava");
//
//            Dog oscar = new Dog();
//            oscar.setName("Oscar");
//
//            Dog pheobe = new Dog();
//            pheobe.setName("Pheobe");
//
//            session.store(arava);
//            session.store(oscar);
//            session.store(pheobe);
//
//            //dogs/1 => dogs/2
//            arava.setLikes(new String[] { oscar.getId() });
//            arava.setDislikes(new String[] { pheobe.getId() });
//
//            //dogs/2 => dogs/2,dogs/3 (cycle!)
//            oscar.setLikes(new String[] { oscar.getId(), pheobe.getId() });
//            oscar.setDislikes(new String[0]);
//
//            //dogs/3 => dogs/2
//            pheobe.setLikes(new String[] { oscar.getId() });
//            pheobe.setDislikes(new String[] { arava.getId() });
//
//            session.saveChanges();
//        }
//    }
//
//    protected static void createMoviesData(IDocumentStore store) {
//        try (IDocumentSession session = store.openSession()) {
//            Genre scifi = new Genre();
//            scifi.setId("genres/1");
//            scifi.setName("Sci-Fi");
//
//            Genre fantasy = new Genre();
//            fantasy.setId("genres/2");
//            fantasy.setName("Fantasy");
//
//            Genre adventure = new Genre();
//            adventure.setId("genres/3");
//            adventure.setName("Adventure");
//
//            session.store(scifi);
//            session.store(fantasy);
//            session.store(adventure);
//
//            Movie starwars = new Movie();
//            starwars.setId("movies/1");
//            starwars.setName("Star Wars Ep.1");
//            starwars.setGenres(Arrays.asList("genres/1", "genres/2"));
//
//            Movie firefly = new Movie();
//            firefly.setId("movies/2");
//            firefly.setName("Firefly Serenity");
//            firefly.setGenres(Arrays.asList("genres/2", "genres/3"));
//
//            Movie indianaJones = new Movie();
//            indianaJones.setId("movies/3");
//            indianaJones.setName("Indiana Jones and the Temple Of Doom");
//            indianaJones.setGenres(Arrays.asList("genres/3"));
//
//            session.store(starwars);
//            session.store(firefly);
//            session.store(indianaJones);
//
//            User user1 = new User();
//            user1.setId("users/1");
//            user1.setName("Jack");
//
//            User.Rating rating11 = new User.Rating();
//            rating11.setMovie("movies/1");
//            rating11.setScore(5);
//
//            User.Rating rating12 = new User.Rating();
//            rating12.setMovie("movies/2");
//            rating12.setScore(7);
//
//            user1.setHasRated(Arrays.asList(rating11, rating12));
//            session.store(user1);
//
//            User user2 = new User();
//            user2.setId("users/2");
//            user2.setName("Jill");
//
//            User.Rating rating21 = new User.Rating();
//            rating21.setMovie("movies/2");
//            rating21.setScore(7);
//
//            User.Rating rating22 = new User.Rating();
//            rating22.setMovie("movies/3");
//            rating22.setScore(9);
//
//            user2.setHasRated(Arrays.asList(rating21, rating22));
//
//            session.store(user2);
//
//            User user3 = new User();
//            user3.setId("users/3");
//            user3.setName("Bob");
//
//            User.Rating rating31 = new User.Rating();
//            rating31.setMovie("movies/3");
//            rating31.setScore(5);
//
//            user3.setHasRated(Arrays.asList(rating31));
//
//            session.store(user3);
//
//            session.saveChanges();
//        }
//    }
}