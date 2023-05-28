<!DOCTYPE html>
<html>
<head>
    <title>Hangman Game</title>
    <style>
        body {
            background-image: url('./images/background_image.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            padding-top: 30px;
        }

        h1 {
            font-size: 40px;
            margin-bottom: 30px;
        }

        .hangman-image {
            display: block;
            margin: 20px auto;
            max-width: 400px;
        }
        
        .word-blanks {
            font-size: 30px;
            margin-bottom: 40px;
            margin-top:40px;
        }

        .word-blanks span {
            border-bottom: 4px solid lightblue;
            padding: 0 10px;
            margin-right: 10px;
        }

        .word-blanks span.guessed {
            color: green;
            border-bottom-color: white;
        }

        .guess-form {
            margin-bottom: 20px;
        }

        .guess-form label {
            display: block;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .guess-form input[type="text"] {
            padding: 10px;
            font-size: 18px;
        }

        .guess-form input[type="submit"] {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
        }

        .guess-form input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .new-game-link {
            font-size: 18px;
            margin-top: 20px;
        }

        .new-game-link a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4CAF50;
            border: none;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .new-game-link a:hover {
            background-color: #45a049;
        }

        .guess-info {
            font-size: 18px;
            margin-top: 20px;
        }

        .guess-info span {
            font-weight: bold;
            color: #ff0000;
        }
    </style>
</head>
<body>
    <h1>Hangman Game</h1>

    <?php
    session_start();

    // Check if a new game needs to be started
    if (!isset($_SESSION['word']) || isset($_GET['newgame'])) {
        // Load the list of words from the file
        $words = file('hangman_words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Choose a random word from the list with a length of at least 5 letters
        $randomIndex = 0;
        do {
            $randomIndex = array_rand($words);
            $_SESSION['word'] = strtoupper($words[$randomIndex]);
        } while (strlen($_SESSION['word']) < 5);

        $_SESSION['blanks'] = str_repeat('_', strlen($_SESSION['word']));
        $_SESSION['wrongGuesses'] = 0;
        $_SESSION['guessedLetters'] = array();
    }

    // Process the letter guess
    if (isset($_GET['thelet'])) {
        $letter = strtoupper($_GET['thelet']);
        $word = $_SESSION['word'];
        $blanks = $_SESSION['blanks'];
        $guessedLetters = $_SESSION['guessedLetters'];

        // Check if the letter is already guessed
        if (in_array($letter, $guessedLetters)) {
            echo '<script>alert("The letter ' . $letter . ' is already guessed.")</script>';
        } elseif (strpos($word, $letter) !== false) {
            // Add the letter to the guessed letters
            $_SESSION['guessedLetters'][] = $letter;

            // Update the blanks with the correctly guessed letter
            for ($i = 0; $i < strlen($word); $i++) {
                if ($word[$i] === $letter) {
                    $blanks[$i] = $letter;
                }
            }
            $_SESSION['blanks'] = $blanks;
        } else {
            // Increment the wrong guesses count
            $_SESSION['wrongGuesses']++;
            // Add the letter to the guessed letters
            $_SESSION['guessedLetters'][] = $letter;
        }
    }

    // Display hangman image
    $wrongGuesses = $_SESSION['wrongGuesses'];
    $hangmanImageURL = "./images/hangman{$wrongGuesses}.png";
    echo '<img src="' . $hangmanImageURL . '" alt="Hangman Image" class="hangman-image">';

    // Display word blanks
    echo '<div class="word-blanks">';
    foreach (str_split($_SESSION['blanks']) as $index => $letter) {
        if ($letter === '_') {
            echo '<span>' . $letter . '</span>';
        } else {
            echo '<span class="guessed">' . $letter . '</span>';
        }
    }
    echo '</div>';

    // Check game status
    $gameOver = false;
    if ($_SESSION['blanks'] === $_SESSION['word']) {
        echo '<p class="message">Congratulations! You won!</p>';
        $gameOver = true;
    } elseif ($_SESSION['wrongGuesses'] >= 6) {
        echo '<p class="message">Game over! You lost. The word was ' . $_SESSION['word'] . '</p>';
        $gameOver = true;
    }

    // Display the guess form if the game is still ongoing
    if (!$gameOver) {
        echo '<form class="guess-form" action="" method="get">';
        echo '<label for="thelet">Guess a letter:</label>';
        echo '<input type="text" id="thelet" name="thelet" maxlength="1" required>';
        echo '<input type="submit" value="Guess">';
        echo '</form>';
    }

    // Display remaining guesses and total guesses
    $remainingGuesses = 6 - $_SESSION['wrongGuesses'];
    echo '<p class="guess-info">Remaining Guesses: <span>' . $remainingGuesses . '</span> Total Guesses: <span>6</span></p>';

    // Display new game link
    echo '<p class="new-game-link"><a href="?newgame">Start a New Game</a></p>';
    ?>

</body>
</html>
