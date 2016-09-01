<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

<div class="row">
    <div class="column column-8 column-offset-2">
        For purpose of this tutorial, we will build simple expanse tracking
        application. Every application development proccess include at least few
        steps, and we will divide our process into:
        <ol>
            <li>database design and table creation</li>
            <li>implementing CRUD model on our tables</li>
            <li>implementing reports for specific period</li>
            <li>implement user authentification and role based access</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">1. configure database access</h1>
        Open <span style="color: #A52A2A;">protected/config/config.php</span>
        file and configure access to your database server like this:
        <pre>
            <code class="php">
                // database driver
                WsConfig::set('db_driver', 'pgsql');
                // database host address
                WsConfig::set('db_host', 'localhost');
                // database port number
                WsConfig::set('db_port', '5432');
                // database name
                WsConfig::set('db_name', 'webiness');
                // database user name
                WsConfig::set('db_user', 'webiness');
                // database user password
                WsConfig::set('db_password', 'webiness');
            </code>
        </pre>
        In our example we use PostgreSQL server on localhost and database name,
        user and user password are all configured to "webiness". If you preferr
        MySql or MariaDB database server your configuration should look like:
        <pre>
            <code class="php">
                // database driver
                WsConfig::set('db_driver', 'mysql');
                // database host address
                WsConfig::set('db_host', 'localhost');
                // database port number
                WsConfig::set('db_port', '3306');
                // database name
                WsConfig::set('db_name', 'webiness');
                // database user name
                WsConfig::set('db_user', 'webiness');
                // database user password
                WsConfig::set('db_password', 'webiness');
            </code>
        </pre>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">2. design database</h1>
        For our small project we will use two database tables. First will
        contain expanse categories and other will contain expanse records it
        self. If you use PostgreSQl enter this SQL code:
        <pre>
            <code class="sql">
                CREATE TABLE expense_type (
                    id SERIAL PRIMARY KEY,
                    name VARCHAR(32) NOT NULL,
                    description TEXT
                );

                CREATE TABLE expense (
                    id SERIAL PRIMARY KEY,
                    expense_type INTEGER NOT NULL
                        REFERENCES expense_type(id) ON DELETE CASCADE ON UPDATE CASCADE,
                    entry_date DATE NOT NULL,
                    value NUMERIC NOT NULL
                );
            </code>
        </pre>
        and if you are using MySql or MariaDB then:
        <pre>
            <code class="sql">
                CREATE TABLE expense_type (
                    id INTEGER NOT NULL AUTO_INCREMENT,
                    name VARCHAR(32) NOT NULL,
                    description TEXT,
                    PRIMARY KEY (id)
                )ENGINE = InnoDB;

                CREATE TABLE expense (
                    id INTEGER NOT NULL AUTO_INCREMENT,
                    expense_type INTEGER NOT NULL,
                    entry_date DATE NOT NULL,
                    value NUMERIC NOT NULL,
                    PRIMARY KEY(id),
                    CONSTRAINT `fk_expense_type`
                        FOREIGN KEY (expense_type) REFERENCES expense_type (id)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                )ENGINE = InnoDB;
            </code>
        </pre>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">3. models</h1>
        Models are part of <a target="_blank" href="http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">MVC</a>
        architecture. They are objects representing business data, rules and
        logic. In Webiness framework, you create model classes by extending
        <a target="_blank" href="<?php echo WsSERVER_ROOT.'/doc/class-WsModel.html'; ?>">
            WsModel
        </a> class.
        <h3 class="text-primary">3.1. creating model</h3>
        Create two files that will refference to data in our
        <strong>expense_type</strong> and <strong>expense</strong> database tables.
        <br/>
        <br/>
        <span style="color: #A52A2A;">
            application/Models/Expense_typeModel.php:
        </span>
        <pre>
            <code class="php">
                class Expense_typeModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();
                    }
                }
            </code>
        </pre>
        <span style="color: #A52A2A;">
            application/Models/ExpenseModel.php:
        </span>
        <pre>
            <code class="php">
                class ExpenseModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();
                    }
                }
            </code>
        </pre>
        <h3 class="text-primary">3.2. attributes</h3>
        Models represent business data in terms of attributes. Each attribute is
        like a publicly accessible property of a model.
        <br/>
        <br/>
        You can access an attribute like accessing a normal object property:
        <pre>
            <code class="php">
                $model = new Expense_typeModel();

                // "name" is an attribute of Expense_typeModel
                $model->name = 'grocery shop';
                echo $model->name;
            </code>
        </pre>
        By default, if you call parent constructor in your model class
        constructor then your model will be automaticaly filled with attributes
        corresponding to your database table. You can have, also, other
        attributes defined in your model class, based on your needs.
        <h3 class="text-primary">3.3. model classes as Active Record objects</h3>
        <a target="_blank" href="http://en.wikipedia.org/wiki/Active_record_pattern">
            Active Record
        </a> provides object-oriented interface for accessing and manipulating
        data stored in databases.
        <br/>
        <br/>
        You can write the following code to insert a new row into the
        expense_type table:
        <pre>
            <code class="php">
                $model = new Expense_typeModel();

                $model->name = 'grocery shop';
                $model->save();
            </code>
        </pre>
        The above code is equivalent to using the following raw SQL statement,
        which is less intuitive, more error prone, and may even have
        compatibility problems if you are using a different kind of database:
        <pre>
            <code class="php">
                $db = new WsDatabase();

                $db->execute('INSERT INTO expense_type (name) VALUES (:name)', array(
                    ':name' => 'grocery shop'
                ));
            </code>
        </pre>
        In the same manner, like you can store records in database, you can do
        other operations too:
        <pre>
            <code class="php">
                $model = new Expense_typeModel();

                // if record with id 3 exists then update record else store new record
                $model->id = 3;
                $model->name = 'college savings';
                $model->save();

                // get records with id 4
                $model->id = 4;
                $model->getOne();
                echo $model->name;
                echo $model->description;

                // delete record with id 5
                $model->id = 5;
                $model->delete();

                // get all records from table
                $res = $model->getAll();
                var_dump($res);
            </code>
        </pre>
        <h3 class="text-primary">3.4. attribute labels</h3>
        When displaying values or getting input for attributes, you often need
        to display some labels associated with attributes. For example, given an
        attribute named <span style="color: #A52A2A;">name </span>, you may want
        to display a label <span style="color: #A52A2A;">Name of Expense</span>
        which is more user-friendly. By default, attribute labels are
        automatically generated from attribute names and they are stored in
        model array <strong>columnHeaders</strong>. You change default label
        with:
        <pre>
            <code class="php">
                $model = new Expense_typeModel();
                $model->columnHeaders['name'] = 'Name of Expense';
            </code>
        </pre>
        Usualy you want to do this in your model class constructor after calling
        parent constructor:
        <pre>
            <code class="php">
                $this->columnHeaders = array(
                    'name' => 'Name of Expense',
                    'description' => 'Description of Expense'
                );
            </code>
        </pre>
        For applications supporting multiple languages, you may also want to
        translate attribute labels, so our model classes now will look like:
        <pre>
            <code class="php">
                class Expense_typeModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();

                        $this->columnHeaders = array(
                            'name' => WsLocalize::msg('Name of Expense'),
                            'description' => WsLocalize::msg('Description of Expense')
                        );
                    }
                }
            </code>
        </pre>
        <pre>
            <code class="php">
                class ExpenseModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();

                        $this->columnHeaders = array(
                            'expense_type' => WsLocalize::msg('Name of Expense'),
                            'entry_date' => WsLocalize::msg('Date of Expense')
                        );
                    }
                }
            </code>
        </pre>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">4. controllers</h1>
        Controllers are part of
        <a target="_blank" href="http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">MVC</a>
        architecture. They are responsible for processing requests and
        generating responses. Controllers are composed of actions which are the
        most basic units that end users can address and request for execution.
        A controller can have one or multiple actions. An action method is a
        public method in controller class.
        <h3 class="text-primary">4.1. routes</h3>
        End users address actions through the so-called routes. Routes take the
        following format:
        <pre>
            <code>
                ControllerID/ActionID
            </code>
        </pre>
        So if a user requests with the URL
        <span style="color: #A52A2A;">
            http://hostname/index.php?request=site/index
        </span>, the <span style="color: #A52A2A;">index</span> action in the
        <span style="color: #A52A2A;">site</span> controller will be executed.
        <h3 class="text-primary">4.2. creating controller</h3>
        Create file with controller class that will contain three actions.
        <br/>
        <br/>
        <span style="color: #A52A2A;">
            application/Controllers/ExpenseController.php:
        </span>
        <pre>
            <code class="php">
                class ExpenseController extends WsController
                {
                    public function expense_types()
                    {
                        $this->render('expense_types');
                    }

                    public function expenses()
                    {
                        $this->render('expenses');
                    }

                    public function expense_report()
                    {
                        $this->render('expense_report');
                    }
                }
            </code>
        </pre>
        We are now created three routes in our application
        <ol>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expense_types'); ?>">
                    <?php echo WsUrl::link('expense', 'expense_types') ?>
                </a>
            </li>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expenses'); ?>">
                    <?php echo WsUrl::link('expense', 'expenses') ?>
                </a>
            </li>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expense_report'); ?>">
                    <?php echo WsUrl::link('expense', 'expense_report') ?>
                </a>
            </li>
        </ol>
        All our actions respond to request by rendering view file. If you follow
        our tutorial step-by-step, when you enter listed links in your browser,
        you should see an user error that indicates that view file is not
        available.
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">5. views</h1>
        Views are part of
        <a target="_blank" href="http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">MVC</a>
        architecture.  They are code responsible for presenting data to end
        users. In a Web application, views are usually created in terms of view
        templates which are PHP script files containing mainly HTML code and
        presentational PHP code.
        <br/>
        <br/>
        Webiness framework use templates located in
        <span style="color: #A52A2A;">
            public/layouts
        </span> directory. Name of default template file is defined in
        <span style="color: #A52A2A;">
            protected/config/config.php
        </span> file:
        <pre>
            <code class="php">
                // default layout file for HTML/PHP rendering
                WsConfig::set('html_layout', 'webiness.php');
            </code>
        </pre>
        If you want to change layout file you can change that line in
        <span style="color: #A52A2A;">
            protected/config/config.php
        </span> file or you can set <strong>layout</strong> property in
        controller class action:
        <pre>
            <code class="php">
                class ExpenseController extends WsController
                {
                    public function expense_types()
                    {
                        // change default layout file for this action
                        $this->layout = 'my_layout';

                        $this->render('expense_types');
                    }
                }
            </code>
        </pre>
        <h3 class="text-primary">5.1. organizing views</h3>
        For views rendered by a controller, they should be put under the
        directory
        <span style="color: #A52A2A;">
            applications/views/ControllerID
        </span> by default, where ControllerID refers to the lowercased
        controller name without <span style="color: #A52A2A;">Controller</span>
        suffix.
        <br/>
        <br/>
        For example, if the controller class is
        <span style="color: #A52A2A;">
            PostController
        </span>, the directory would be
        <span style="color: #A52A2A;">application/views/post</span>.
        <h3 class="text-primary">5.2. creating views</h3>
        Create sub directory <span style="color: #A52A2A;">expense</span> in
        <span style="color: #A52A2A;">applications/views</span> directory and
        put these three files in it:
        <span style="color: #A52A2A;">
            applications/views/expense/expense_types.php
        </span>
        <pre>
            <code class="php">
                echo 'here will be page for editing expense types';
            </code>
        </pre>
        <span style="color: #A52A2A;">
            applications/views/expense/expenses.php
        </span>
        <pre>
            <code class="php">
                echo 'here will be page for editing expenses';
            </code>
        </pre>
        <span style="color: #A52A2A;">
            applications/views/expense/expenses.php
        </span>
        <pre>
            <code class="php">
                echo 'here will be page that will show how much money we spend in some period';
            </code>
        </pre>
        If you now point browser to our previously defined routes, you should
        see text that we put in our echo statments.
        <ol>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expense_types'); ?>">
                    <?php echo WsUrl::link('expense', 'expense_types') ?>
                </a>
            </li>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expenses'); ?>">
                    <?php echo WsUrl::link('expense', 'expenses') ?>
                </a>
            </li>
            <li>
                <a target="_blank" href="<?php echo WsUrl::link('expense', 'expense_report'); ?>">
                    <?php echo WsUrl::link('expense', 'expense_report') ?>
                </a>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <h1 class="text-primary">6. CRUD</h1>
        Open again our controller file
        <span style="color: #A52A2A;">
            application/controller/ExpenseController.php
        </span> and change code in <strong>expense_types()</strong> and
        <strong>expenses()</strong> actions to:
        <pre>
            <code class="php">
                public function expense_types()
                {
                    // create model
                    $model = new Expense_typeModel();

                    // send model to view
                    $this->render('expense_types', array(
                        'model' => $model
                    ));
                }

                public function expenses()
                {
                    // create model
                    $model = new ExpenseModel();

                    // send model to view
                    $this->render('expenses', array(
                        'model' => $model
                    ));
                }
            </code>
        </pre>
        Webiness framework has special class
        <a target="_blank" href="doc/class-WsModelGridView.html">
            WsModelGridView
        </a> which will do magic and create AJAX based CRUD for your models in
        just two lines of code. So, change our views to this:
        <br/>
        <br/>
        <span style="color: #A52A2A;">
            applications/views/expense/expense_types.php
        </span>
        <pre>
            <code class="xml">
                &lt;div class="row"&gt;
                    &lt;div class="column column-8 column-offset-2"&gt;
                    &lt;?php
                        $grid = new WsModelGridView($model);
                        $grid->show();
                    ?&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            </code>
        </pre>
        <span style="color: #A52A2A;">
            applications/views/expense/expense_types.php
        </span>
        <pre>
            <code class="xml">
                &lt;div class="row"&gt;
                    &lt;div class="column column-8 column-offset-2"&gt;
                    &lt;?php
                        $grid = new WsModelGridView($model);
                        $grid->show();
                    ?&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            </code>
        </pre>
        Now, if you open your application, you will see grid with data columns:
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud1.png'); ?>"/>
        </div>
        <br/>
        <br/>
        Grid has automatic ability for adding new items, searching, editing
        existing items and removal of existing items:
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-add.png'); ?>"/>
            <br/>
            <small>(adding a new item)</small>
        </div>
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-edit.png'); ?>"/>
            <br/>
            <small>(editing an existing item)</small>
        </div>
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-delete.png'); ?>"/>
            <br/>
            <small>(removing an existing item)</small>
        </div>
        <br/>
        <br/>
        <h3 class="text-primary">6.1. customizing CRUD</h3>
        When you open second page and try to add new item:
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-add2.png'); ?>"/>
            <br/>
            <small>(removing an existing item)</small>
        </div>
        <br/>
        <br/>
        you notice selection input control for foreign key. Like we sad before,
        model classes automaticaly detects foreign keys but in list we see
        IDs of foreign keys and that is not much user friendly and in most cases
        is not what you want.
        <br/>
        <br/>
        To change that we will go back to <span style="color: #A52A2A;">
            application/Models/ExpenseModel.php
        </span> file and add foreign key description array to our
        <strong>ExpenseModel</strong> class:
        <pre>
            <code class="php">
                class ExpenseModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();

                        $this->columnHeaders = array(
                            'expense_type' => WsLocalize::msg('Expense Type'),
                            'entry_date' => WsLocalize::msg('Date of expense'),
                            'value' => WsLocalize::msg('Value'),
                        );

                        // show value of 'name' column when displaying foreign key
                        $this->foreignKeys['expense_type']['display'] = 'name';
                    }
                }
            </code>
        </pre>
        now we get desired values in our foreign key selection box:
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-add3.png'); ?>"/>
        </div>
        <br/>
        <br/>
        also, we will change grids and dialog title so that it don't shows
        generic table name, and we will hide ID column from user:
        <pre>
            <code class="php">
                class ExpenseModel extends WsModel
                {
                    public function __construct()
                    {
                        parent::__construct();

                        $this->columnHeaders = array(
                            'expense_type' => WsLocalize::msg('Expense Type'),
                            'entry_date' => WsLocalize::msg('Date of expense'),
                            'value' => WsLocalize::msg('Value'),
                        );

                        // show value of 'name' column when displaying
                        $this->foreignKeys['expense_type']['display'] = 'name';

                        // set title
                        $this->metaName = 'My Expenses';

                        // hide ID column
                        $this->hiddenColumns = array(
                            'id'
                        );
                    }
                }
            </code>
        </pre>
        and final result will be:
        <br/>
        <br/>
        <div class="text-center">
        <img style="border: 1px solid #1E90FF"
            width="560" src="<?php echo WsUrl::asset('img/guide/crud-add4.png'); ?>"/>
        </div>
    </div>
</div>

<div class="row">
    <br/>
    <br/>
</div>

<div class="row">
    <div class="column column-8 column-offset-2">
        <div class="callout warning">
            <strong>Note: </strong><br/><br/>
            In all our examples, and framework itself, we use coding style:
            <pre>
                <code class="php">
                    $arr = array(item1, item2, ...);

                    &lt;?php echo $var; ?&gt;
                </code>
            </pre>
            instead of:
            <pre>
                <code class="php">
                    $arr = [item1, item2, ...];

                    &lt;?= $var ?&gt;
                </code>
            </pre>
            That's becouse we want to be compatible with lowest version of
            PHP that we can, without using any deprecated features or need
            to edit php.ini file and etc.
            <br/>
            <br/>
            Webiness is compatible with PHP 5.3 and PHP 7.0 and higher, so you
            can use short array syntax and other modern elements of PHP language
            that are supported with your PHP version.
        </div>
        <br/><br/><br/>
        Now you are familiar with Webiness basics. Read our
        <a href="<?php echo WsUrl::link('site', 'auth_guide'); ?>">user
        authentication guide</a> to learn how to use internal user authentication, authorization
        and role based access control system.
    </div>
</div>
