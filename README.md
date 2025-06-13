# DesignPatternsPHP

This is a collection of known design patterns and some sample codes on how to implement them in PHP. Every pattern has a small list of examples.

I think the problem with patterns is that often people do know them but don't know when to apply which. Remember that each pattern has its own trade-offs. And you need to pay attention more to why you're choosing a certain pattern than to how to implement it.

## Installation
You should look at and run the tests to see what happens in the example.
To do this, you should install dependencies with `Composer` first:

```bash
$ composer install
```

Read more about how to install and use `Composer` on your local machine [here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

To run the tests use `phpunit`:

```bash
$ ./vendor/bin/phpunit
```

## Patterns

The patterns can be structured in roughly three different categories. Please click on the [:notebook:](http://en.wikipedia.org/wiki/Software_design_pattern) for a full explanation of the pattern on Wikipedia.

### [Structural](Structural)

* [Adapter](Structural/Adapter) [:notebook:](http://en.wikipedia.org/wiki/Adapter_pattern)
* [Bridge](Structural/Bridge) [:notebook:](http://en.wikipedia.org/wiki/Bridge_pattern)