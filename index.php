<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quiz_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, question, option1, option2, option3, correct_option, explanation FROM questions ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question {
            display: none;
        }

        .question.active {
            display: block;
        }

        .explanation {
            display: none;
        }

        .explanation.correct {
            background-color: #fff;
        }

        .explanation.incorrect {
            background-color: #fff;
        }

        .correct-answer {
            color: green;
        }

        .wrong-answer {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div id="quiz" class="card">
            <div class="card-body">
                <?php foreach ($questions as $index => $question) : ?>
                    <div class="question" id="question<?php echo $index; ?>">
                        <h5 class="card-title"><?php echo ($index + 1) . ". " . $question['question']; ?></h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question<?php echo $index; ?>" value="1">
                            <label class="form-check-label" id="label-<?php echo $index; ?>-1"><?php echo $question['option1']; ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question<?php echo $index; ?>" value="2">
                            <label class="form-check-label" id="label-<?php echo $index; ?>-2"><?php echo $question['option2']; ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question<?php echo $index; ?>" value="3">
                            <label class="form-check-label" id="label-<?php echo $index; ?>-3"><?php echo $question['option3']; ?></label>
                        </div>
                        <button class="btn btn-primary mt-3" type="button" onclick="checkAnswer(<?php echo $index; ?>)">Jawab</button>
                    </div>

                    <div class="explanation" id="explanation<?php echo $index; ?>">
                        <p id="explanationText<?php echo $index; ?>"></p>
                        <p><?php echo $question['explanation']; ?></p>
                        <button class="btn btn-secondary mt-3" type="button" onclick="nextQuestion(<?php echo $index; ?>)">Berikutnya</button>
                    </div>
                <?php endforeach; ?>
                <div id="result" class="alert mt-3" style="display:none;"></div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        let currentQuestion = 0;
        let score = 0;
        const questions = <?php echo json_encode($questions); ?>;

        function showQuestion(index) {
            if (index < questions.length) {
                document.getElementById('question' + index).classList.add('active');
            }
        }

        function checkAnswer(questionIndex) {
            const options = document.getElementsByName('question' + questionIndex);
            let selectedOption;
            for (const option of options) {
                if (option.checked) {
                    selectedOption = option.value;
                }
            }

            const explanationElement = document.getElementById('explanation' + questionIndex);
            const explanationTextElement = document.getElementById('explanationText' + questionIndex);

            if (selectedOption === questions[questionIndex].correct_option.toString()) {
                score++;
                explanationElement.classList.add('correct');
                explanationTextElement.textContent = "Pembahasan : ";
            } else {
                explanationElement.classList.add('incorrect');
                explanationTextElement.textContent = "Pembahasan : ";
            }

            explanationElement.style.display = 'block';

            // Highlight the correct and wrong answers
            for (let i = 1; i <= 4; i++) {
                const label = document.getElementById('label-' + questionIndex + '-' + i);
                if (i == questions[questionIndex].correct_option) {
                    label.classList.add('correct-answer');
                } else {
                    label.classList.add('wrong-answer');
                }
            }
        }

        function nextQuestion(questionIndex) {
            document.getElementById('question' + questionIndex).style.display = 'none';
            document.getElementById('explanation' + questionIndex).style.display = 'none';
            currentQuestion++;
            if (currentQuestion < questions.length) {
                showQuestion(currentQuestion);
            } else {
                document.getElementById('result').textContent = "Anda Benar " + score + " dari " + questions.length + " soal";
                document.getElementById('result').style.display = 'block';
            }
        }

        window.onload = function() {
            showQuestion(0);
        };
    </script>
</body>

</html>