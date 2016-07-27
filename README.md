# Bacon

Setup:  
1) Fork  
2) Clone  
3) Checkout `develop`  
4) Branch  
5) Code  
6) Submit Pull Request  

## <a id="testing"></a>Testing

Presuming you have set up [Composer](#composer), and Giftbox or Hermes.
To run all the tests, just reference the configuration file:

    cd ../../
    vendor/bin/phpunit --bootstrap src/tests/autoload.php -c tests.xml

To also investigate the code coverage of the tests, you'll need the
[Xdebug PHP extension](http://xdebug.org/docs/install).
Make sure you put any unit tests in the `Tests` directory and name them like
MyAwesomeTest.php.


## Deployment

The repository is automagically deployed to development at [dev.gosizzle.io](http://dev.gosizzle.io).
