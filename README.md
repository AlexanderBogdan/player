# Wimi Online - PHP test #
 
## How to start ##
- Install the dependencies with composer ```you_should_know_this_command```
- Start the build-in webserver ```bin/console server:start```
- Verify the REST api is working ```http://localhost:8000/players```

### Task 1 ###
- Let's run the tests! ```bin/phpspec run```
- As you might noticed, there is one failing test... Let's fix it!

### Task 2 ###
In the existing code we can retrieve, patch and update the players. We also would like to create new players.
- Create the code to add a new players with an API call
- _Extra points if you also write the phpspec tests (TDD)_

### Task 3 ###
Everyone likes clean code!
- Let's fix the file ```src/Service/Player.php```

_We prefer the PSR-1/2 and Symfony standards._
