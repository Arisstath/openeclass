<?php
/* ========================================================================
 * Open eClass 3.7
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2019  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */


include 'exercise.class.php';
include 'question.class.php';
include 'answer.class.php';

$require_current_course = TRUE;
$guest_allowed = true;
include '../../include/baseTheme.php';
require_once 'include/lib/textLib.inc.php';
require_once 'modules/gradebook/functions.php';
require_once 'game.php';
require_once 'analytics.php';

$pageName = $langExercicesResult;
$navigation[] = array('url' => "index.php?course=$course_code", 'name' => $langExercices);

# is this an AJAX request to check grades?
$checking = false;
$ajax_regrade = false;

// picture path
$picturePath = "courses/$course_code/image";
// Identifying ajax request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $is_editor) {
    if (isset($_GET['check'])) {
        $checking = true;
        header('Content-Type: application/json');
    } elseif (isset($_POST['regrade'])) {
        $ajax_regrade = true;
    } else {
        $grade = $_POST['question_grade'];
        $question_id = $_POST['question_id'];
        $eurid = $_GET['eurId'];
        Database::get()->query("UPDATE exercise_answer_record
                    SET weight = ?f WHERE eurid = ?d AND question_id = ?d",
            $grade, $eurid, $question_id);
        $ungraded = Database::get()->querySingle("SELECT COUNT(*) AS count
            FROM exercise_answer_record WHERE eurid = ?d AND weight IS NULL",
            $eurid)->count;
        if ($ungraded == 0) {
            // if no more ungraded questions, set attempt as complete and
            // recalculate sum of grades
            Database::get()->query("UPDATE exercise_user_record
                SET attempt_status = ?d,
                    total_score = (SELECT SUM(weight) FROM exercise_answer_record
                                        WHERE eurid = ?d)
                WHERE eurid = ?d",
                ATTEMPT_COMPLETED, $eurid, $eurid);            
        } else {
            // else increment total by just this grade
            Database::get()->query("UPDATE exercise_user_record
                SET total_score = total_score + ?f WHERE eurid = ?d",
                $grade, $eurid);
        }        
        $data = Database::get()->querySingle("SELECT eid, uid, total_score, total_weighting 
                             FROM exercise_user_record WHERE eurid = ?d", $eurid);
            // update gradebook
        update_gradebook_book($data->uid, $data->eid, $data->total_score/$data->total_weighting, GRADEBOOK_ACTIVITY_EXERCISE);        
        triggerGame($course_id, $uid, $data->eid);
        triggerExerciseAnalytics($course_id, $uid, $data->eid);
        exit();
    }
}

require_once 'include/lib/modalboxhelper.class.php';
require_once 'include/lib/multimediahelper.class.php';
ModalBoxHelper::loadModalBox();

load_js('tools.js');

if (isset($_GET['eurId'])) {
    $eurid = $_GET['eurId'];
    $exercise_user_record = Database::get()->querySingle("SELECT * FROM exercise_user_record WHERE eurid = ?d", $eurid);
    $exercise_question_ids = Database::get()->queryArray("SELECT DISTINCT question_id
                                                        FROM exercise_answer_record WHERE eurid = ?d", $eurid);
    $user = Database::get()->querySingle("SELECT * FROM user WHERE id = ?d", $exercise_user_record->uid);
    if (!$exercise_user_record) {
        // No record matches with this exercise user record id
        Session::Messages($langExerciseNotFound);
        redirect_to_home_page('modules/exercise/index.php?course='.$course_code);
    }
    if (!$is_editor && $exercise_user_record->uid != $uid || $exercise_user_record->attempt_status == ATTEMPT_PAUSED) {
       // student is not allowed to view other people's exercise results
       // Nobody can see results of a paused exercise
       redirect_to_home_page('modules/exercise/index.php?course='.$course_code);
    }
    $objExercise = new Exercise();
    $objExercise->read($exercise_user_record->eid);
} else {
    // exercise user recird id is not set
    redirect_to_home_page('modules/exercise/index.php?course='.$course_code);
}
if ($is_editor && ($exercise_user_record->attempt_status == ATTEMPT_PENDING || $exercise_user_record->attempt_status == ATTEMPT_COMPLETED)) {
    $head_content .= "<script type='text/javascript'>
            $(document).ready(function(){
                    function save_grade(elem){
                        var grade = parseFloat($(elem).val());
                        var element_name = $(elem).attr('name');
                        var questionId = parseInt(element_name.substring(14,element_name.length - 1));
                        var questionMaxGrade = parseFloat($(elem).next().val());
                        if (grade > questionMaxGrade) {
                            bootbox.alert('$langGradeTooBig');
                            return false;
                        } else if (isNaN(grade)){
                            $(elem).css({'border-color':'red'});
                            return false;
                        } else {
                            $.ajax({
                              type: 'POST',
                              url: '',
                              data: {question_grade: grade, question_id: questionId},
                            });
                            $(elem).parent().prev().hide();
                            $(elem).prop('disabled', true);
                            $(elem).css({'border-color':'#dfdfdf'});
                            var prev_grade = parseInt($('span#total_score').html());
                            var updated_grade = prev_grade + grade;
                            $('span#total_score').html(updated_grade);
                            return true;
                        }
                    }
                    $('.questionGradeBox').keyup(function (e) {
                        if (e.keyCode == 13) {
                            save_grade(this);
                            var countnotgraded = $('input.questionGradeBox').not(':disabled').length;
                            if (countnotgraded == 0) {
                                $('a#submitButton').hide();
                                $('a#all').hide();
                                $('a#ungraded').hide();
                                $('table.graded').show('slow');
                            }
                        }
                    });
                    $('a#submitButton').click(function(e){
                        e.preventDefault();
                        var success = true;
                        $('.questionGradeBox').each(function() {
                           success = save_grade(this);
                        });
                        if (success) {
                         $(this).parent().hide();
                        }
                    });
                    $('a#ungraded').click(function(e){
                        e.preventDefault();
                        $('a#all').removeClass('btn-primary').addClass('btn-default');
                        $(this).removeClass('btn-default').addClass('btn-primary');
                        $('table.graded').hide('slow');
                    });
                    $('a#all').click(function(e){
                        e.preventDefault();
                        $('a#ungraded').removeClass('btn-primary').addClass('btn-default');
                        $(this).removeClass('btn-default').addClass('btn-primary');
                        $('table.graded').show('slow');
                    });
                });
                </script>";
}
$exerciseTitle = $objExercise->selectTitle();
$exerciseDescription = nl2br(make_clickable($objExercise->selectDescription()));
$exerciseDescription_temp = mathfilter(nl2br(make_clickable($exerciseDescription)), 12, "../../courses/mathimg/");
$displayResults = $objExercise->selectResults();
$displayScore = $objExercise->selectScore();
$exerciseAttemptsAllowed = $objExercise->selectAttemptsAllowed();
$userAttempts = Database::get()->querySingle("SELECT COUNT(*) AS count FROM exercise_user_record WHERE eid = ?d AND uid= ?d", $exercise_user_record->eid, $uid)->count;

$cur_date = new DateTime("now");
$end_date = new DateTime($objExercise->selectEndDate());

$showResults = $displayResults == 1
               || $is_editor
               || $displayResults == 3 && $exerciseAttemptsAllowed == $userAttempts
               || $displayResults == 4 && $end_date < $cur_date;

$showScore = $displayScore == 1
            || $is_editor
            || $displayScore == 3 && $exerciseAttemptsAllowed == $userAttempts
            || $displayScore == 4 && $end_date < $cur_date;

$tool_content .= "<div class='panel panel-primary'>
  <div class='panel-heading'>
    <h3 class='panel-title'>" . q_math($exerciseTitle) . "</h3>
  </div>
  <div class='panel-body'>";

if (!empty($exerciseDescription_temp)) {
    if ($exerciseDescription_temp) {
        $tool_content .= $exerciseDescription_temp."<hr>";
    }
}

if (!isset($user)) {
    $tool_content .= "
        <div class='row'>
            <div class='col-xs-6 col-md-3 text-right'>
                <strong>$langSurname:</strong>
            </div>
            <div class='col-xs-6 col-md-3'>
                " . q($user->surname) . "
            </div>
            <div class='col-xs-6 col-md-3 text-right'>
                <strong>$langName:</strong>
            </div>
            <div class='col-xs-6 col-md-3'>
                " . q($user->givenname) . "
            </div>";
            if ($user->am) {
                $tool_content .= "
            <div class='col-xs-6 col-md-3 text-right'>
                <strong>$langAm:</strong>
            </div>
            <div class='col-xs-6 col-md-3'>
                " . q($user->am) . "
            </div>";
            }
            if ($user->phone) {
                $tool_content .= "
            <div class='col-xs-6 col-md-3 text-right'>
                <strong>$langPhone:</strong>
            </div>
            <div class='col-xs-6 col-md-3'>
                " . q($user->phone) . "
            </div>";
            }
            if ($user->email) {
                $tool_content .= "
            <div class='col-xs-6 col-md-3 text-right'>
                <strong>Email:</strong>
            </div>
            <div class='col-xs-6 col-md-3'>
                " . q($user->email) . "
            </div>";
            }
    $tool_content .= "</div>";
}
$tool_content .= "
    </div>
  </div>
  <div class='row margin-bottom-fat'>
    <div class='col-md-5 col-md-offset-7'>";
if ($is_editor && $exercise_user_record->attempt_status == ATTEMPT_PENDING) {
    $tool_content .= "
            <div class='btn-group btn-group-sm' style='float:right;'>
                <a class='btn btn-primary' id='all'>$langAllExercises</a>
                <a class='btn btn-default' id='ungraded'>$langAttemptPending</a>
            </div>";
}
$tool_content .= "
    </div>
  </div>";
$i = 0;

if ($is_editor and $exercise_user_record->attempt_status == ATTEMPT_COMPLETED and isset($_POST['regrade'])) {
    $regrade = true;
} else {
    $regrade = false;
}

$totalWeighting = $totalScore = 0;

// for each question
if (count($exercise_question_ids) > 0) {
    foreach ($exercise_question_ids as $row) {
        // creates a temporary Question object
        $objQuestionTmp = new Question();
        $is_question = $objQuestionTmp->read($row->question_id);
        // gets the student choice for this question
        $choice = $objQuestionTmp->get_answers_record($eurid);        
        $questionName = $objQuestionTmp->selectTitle();
        $questionDescription = $objQuestionTmp->selectDescription();
        $questionWeighting = $objQuestionTmp->selectWeighting();
        $answerType = $objQuestionTmp->selectType();

        // destruction of the Question object
        unset($objQuestionTmp);
        // check if question has been graded
        $question_weight = Database::get()->querySingle("SELECT SUM(weight) AS weight FROM exercise_answer_record WHERE question_id = ?d AND eurid =?d", $row->question_id, $eurid)->weight;
        $question_graded = is_null($question_weight) ? FALSE : TRUE;

        if ($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER || $answerType == TRUE_FALSE) {
            $colspan = 4;
        } elseif ($answerType == MATCHING) {
            $colspan = 2;
        } else {
            $colspan = 1;
        }
        $iplus = $i + 1;
        $tool_content .= "
            <table class='table-default ".(($question_graded)? 'graded' : 'ungraded')."'>
            <tr class='active'>
              <td colspan='${colspan}'><b><u>$langQuestion</u>: $iplus</b></td>
            </tr>
            <tr>
              <td colspan='${colspan}'>";
        if ($is_question) {
            $tool_content .= "
                <b>" . q_math($questionName) . "</b>
                <br>" .
                standard_text_escape($questionDescription)
                . "<br><br>";
        } else {
            $tool_content .= "<div class='alert alert-warning'>$langQuestionAlreadyDeleted</div>";
        }

        $tool_content .= "
              </td>
            </tr>";
        if (file_exists($picturePath . '/quiz-' . $row->question_id)) {
            $tool_content .= "
                      <tr class='even'>
                        <td class='text-center' colspan='${colspan}'><img src='../../$picturePath/quiz-" . $row->question_id . "'></td>
                      </tr>";
        }

        if ($showResults && !is_null($choice)) {
            if ($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER || $answerType == TRUE_FALSE) {
                $tool_content .= "
                            <tr class='even'>
                              <td width='50' valign='top'><b>$langChoice</b></td>
                              <td width='50' class='center' valign='top'><b>$langExpectedChoice</b></td>
                              <td valign='top'><b>$langAnswer</b></td>
                              <td valign='top'><b>$langComment</b></td>
                            </tr>";
            } elseif ($answerType == FILL_IN_BLANKS || $answerType == FILL_IN_BLANKS_TOLERANT || $answerType == FREE_TEXT) {
                $tool_content .= "
                            <tr class='active'>
                              <td><b>$langAnswer</b></td>
                            </tr>";
            } else {
                $tool_content .= "
                            <tr class='even'>
                              <td><b>$langElementList</b></td>
                              <td><b>$langCorrespondsTo</b></td>
                            </tr>";
            }
        }

        $questionScore = 0;

        if ($answerType != FREE_TEXT) { // if NOT FREE TEXT (i.e. question has answers)
            // construction of the Answer object
            $objAnswerTmp = new Answer($row->question_id);
            $nbrAnswers = $objAnswerTmp->selectNbrAnswers();

            for ($answerId = 1; $answerId <= $nbrAnswers; $answerId++) {
                $answer = $objAnswerTmp->selectAnswer($answerId);
                $answerComment = standard_text_escape($objAnswerTmp->selectComment($answerId));
                $answerCorrect = $objAnswerTmp->isCorrect($answerId);
                $answerWeighting = $objAnswerTmp->selectWeighting($answerId);

                if ($answerType == FILL_IN_BLANKS or $answerType == FILL_IN_BLANKS_TOLERANT) {
                    list($answer, $answerWeighting) = Question::blanksSplitAnswer($answer);
                } else {
                    $answer = standard_text_escape($answer);
                }
                
                $grade = 0;
                switch ($answerType) {
                    // for unique answer
                    case UNIQUE_ANSWER : $studentChoice = ($choice == $answerId) ? 1 : 0;
                        if ($studentChoice) {
                            $questionScore += $answerWeighting;
                            $grade = $answerWeighting;
                        }
                        break;
                    // for multiple answers
                    case MULTIPLE_ANSWER : $studentChoice = @$choice[$answerId];
                        if ($studentChoice) {
                            $questionScore += $answerWeighting;
                            $grade = $answerWeighting;
                        }
                        break;
                    // for fill in the blanks
                    case FILL_IN_BLANKS :
                    case FILL_IN_BLANKS_TOLERANT :
                        // splits weightings that are joined with a comma
                        $answerWeighting = explode(',', $answerWeighting);
                        // we save the answer because it will be modified
                        $temp = $answer;
                        $answer = '';
                        $j = 1;
                        // the loop will stop at the end of the text
                        while (1) {
                            // quits the loop if there are no more blanks
                            if (($pos = strpos($temp, '[')) === false) {
                                // adds the end of the text
                                $answer .= q($temp);
                                break;
                            }
                            // adds the piece of text that is before the blank and ended by [
                            $answer .= substr($temp, 0, $pos + 1);
                            $temp = substr($temp, $pos + 1);
                            // quits the loop if there are no more blanks
                            if (($pos = strpos($temp, ']')) === false) {
                                // adds the end of the text
                                $answer .= q($temp);
                                break;
                            }
                            $choice[$j] = canonicalize_whitespace($choice[$j]);
                            // if the word entered is the same as the one defined by the professor
                            $canonical_choice = $answerType == FILL_IN_BLANKS_TOLERANT ? strtr(mb_strtoupper($choice[$j], 'UTF-8'), "ΆΈΉΊΌΎΏ", "ΑΕΗΙΟΥΩ") : $choice[$j];
                            $canonical_match = $answerType == FILL_IN_BLANKS_TOLERANT ? strtr(mb_strtoupper(substr($temp, 0, $pos), 'UTF-8'), "ΆΈΉΊΌΎΏ", "ΑΕΗΙΟΥΩ") : substr($temp, 0, $pos);
                            $right_answers = array_map('canonicalize_whitespace',
                                preg_split('/\s*\|\s*/', $canonical_match));
                            if (in_array($canonical_choice, $right_answers)) {
                                // gives the related weighting to the student
                                $questionScore += $answerWeighting[$j-1];
                                if ($regrade) {
                                    Database::get()->query('UPDATE exercise_answer_record
                                        SET weight = ?f
                                        WHERE eurid = ?d AND question_id = ?d AND answer_id = ?d',
                                        $answerWeighting[$j-1], $eurid, $row->question_id, $j);
                                }
                                // increments total score
                                // adds the word in green at the end of the string
                                $answer .= '<b>' . q($choice[$j]) . '</b>';
                            }
                            // else if the word entered is not the same as the one defined by the professor
                            elseif ($choice[$j] !== '') {
                                // adds the word in red at the end of the string, and strikes it
                                $answer.='<span class="text-danger"><s>' . q($choice[$j]) . '</s></span>';
                            } else {
                                // adds a tabulation if no word has been typed by the student
                                $answer.='&nbsp;&nbsp;&nbsp;';
                            }
                            // adds the correct word, followed by ] to close the blank
                            $answer .= ' / <span class="text-success"><b>' .
                                q(preg_replace('/\s*,\s*/', " $langOr ", substr($temp, 0, $pos))) .
                                '</b></span>]';
                            $j++;
                            $temp = substr($temp, $pos + 1);
                        }
                        break;
                    // for matching
                    case MATCHING : if ($answerCorrect) {
                            $thisChoice = isset($choice[$answerId])? $choice[$answerId]: null;
                            if ($answerCorrect == $thisChoice) {
                                $questionScore += $answerWeighting;
                                $grade = $answerWeighting;
                                $choice[$answerId] = $matching[$choice[$answerId]];
                            } elseif (!$thisChoice) {
                                $choice[$answerId] = '&nbsp;&nbsp;&nbsp;';
                            } else {
                                $choice[$answerId] = "<span class='text-danger'><del>" .
                                    $matching[$choice[$answerId]] . "</del></span>";
                            }
                        } else {
                            $matching[$answerId] = $answer;
                        }
                        if ($regrade) {
                            Database::get()->query('UPDATE exercise_answer_record
                                SET weight = ?f
                                WHERE eurid = ?d AND question_id = ?d AND answer = ?d',
                                $grade, $eurid, $row->question_id, $answerId);
                        }
                        break;
                    case TRUE_FALSE : $studentChoice = ($choice == $answerId) ? 1 : 0;
                        if ($studentChoice) {
                            $questionScore += $answerWeighting;
                            $grade = $answerWeighting;
                        }
                        break;
                } // end switch()

                if ($regrade and !in_array($answerType, [FILL_IN_BLANKS_TOLERANT, FILL_IN_BLANKS, MATCHING])) {
                    Database::get()->query('UPDATE exercise_answer_record
                        SET weight = ?f
                        WHERE eurid = ?d AND question_id = ?d AND answer_id = ?d',
                        $grade, $eurid, $row->question_id, $answerId);
                }

                if ($showResults) {
                    if ($answerType != MATCHING || $answerCorrect) {
                        if ($answerType == UNIQUE_ANSWER || $answerType == MULTIPLE_ANSWER || $answerType == TRUE_FALSE) {
                            $tool_content .= "
                                                <tr class='even'>
                                                  <td>
                                                  <div align='center'>";

                            if ($studentChoice) {
                                $icon_choice= "fa-check-square-o";
                            } else {
                                $icon_choice = "fa-square-o";
                            }

                            $tool_content .= icon($icon_choice) . "</div>
                                                </td>
                                                <td><div align='center'>";

                            if ($answerCorrect) {
                                $icon_choice= "fa-check-square-o";
                            } else {
                                $icon_choice = "fa-square-o";
                            }
                            $tool_content .= icon($icon_choice)."</div>";
                            $tool_content .= "
                                                </td>
                                                <td>" . standard_text_escape($answer) . "</td>
                                                <td>";
                            if ($studentChoice) {
                                $tool_content .= standard_text_escape(nl2br(make_clickable($answerComment)));
                            } else {
                                $tool_content .= '&nbsp;';
                            }
                            $tool_content .= "</td></tr>";
                        } elseif ($answerType == FILL_IN_BLANKS || $answerType == FILL_IN_BLANKS_TOLERANT) {
                            $tool_content .= "
                                                <tr class='even'>
                                                  <td>" . standard_text_escape(nl2br($answer)) . "</td>
                                                </tr>";
                        } else {
                            $tool_content .= "
                                                <tr class='even'>
                                                  <td>" . standard_text_escape($answer) . "</td>
                                                  <td>" . $choice[$answerId] ." / <span class='text-success'><b>" .
                                                          $matching[$answerCorrect] . "</b></span></td>
                                                </tr>";
                        }
                    }
                } // end of if
            } // end for()
        } else { // If FREE TEXT type
            $tool_content .= "<tr class='even'>
                                 <td>" . purify($choice) . "</td>
                              </tr>";
        }
        $tool_content .= "<tr class='active'>
                            <th colspan='$colspan'>";
        if ($answerType == FREE_TEXT) {
            $choice = purify($choice);
            if (!empty($choice)) {
                if (!$question_graded) {
                    $tool_content .= "<span class='text-danger'>$langAnswerUngraded</span>";
                } else {
                    $questionScore = $question_weight;
                }
            }
        }

        if ($showScore) {
            if (!is_null($choice)) {
                if ($answerType == FREE_TEXT && $is_editor) {
                    if (isset($question_graded) && !$question_graded) {
                        $value = '';                    
                    } else {
                        $value = round($questionScore, 2);
                    }
                    $tool_content .= "<span style='float:right;'>
                                   $langQuestionScore: <input style='display:inline-block;width:auto;' type='text' class='questionGradeBox' maxlength='3' size='3' name='questionScore[$row->question_id]' value='$value'>
                                   <input type='hidden' name='questionMaxGrade' value='$questionWeighting'>
                                   <strong>/$questionWeighting</strong>
                                    </span>";
                }
            } else {
                $tool_content .= "<span style='float:right;'>$langQuestionScore: <b>$question_weight</b></span>";
            }
        }
        $tool_content .= "</th></tr>";

        if ($showScore and $question_weight != $questionScore) {
            $tool_content .= "<tr class='warning'>
                                <th colspan='$colspan' class='text-right'>
                                    $langQuestionStoredScore: " . round($question_weight, 2) . " / $questionWeighting
                                </th>
                              </tr>";

        }

        $tool_content .= "</table>";

        $totalScore += $questionScore;
        $totalWeighting += $questionWeighting;

        // destruction of Answer
        unset($objAnswerTmp);
        $i++;
    } // end foreach()
} else {
    redirect_to_home_page('modules/exercise/index.php?course='.$course_code);
}

if ($regrade) {
    Database::get()->query('UPDATE exercise_user_record
        SET total_score = ?f, total_weighting = ?f
        WHERE eurid = ?d', $totalScore, $totalWeighting, $eurid);
    update_gradebook_book($exercise_user_record->uid,
        $exercise_user_record->eid, $totalScore / $totalWeighting, GRADEBOOK_ACTIVITY_EXERCISE);

    // find all duplicate wrong entries (for questions with type `unique answer)
    $wrong_data = Database::get()->queryArray("SELECT question_id FROM exercise_answer_record
                                            JOIN exercise_question
                                                ON question_id = id
                                                AND `type` = " . UNIQUE_ANSWER . "
                                                AND eurid = ?d
                                            GROUP BY eurid, question_id, answer_id
                                            HAVING COUNT(question_id) > 1", $eurid);
    // delete all duplicate entries
    foreach ($wrong_data as $d) {
        $max_arid = Database::get()->querySingle("SELECT MAX(answer_record_id) AS max_arid FROM exercise_answer_record WHERE eurid=?d AND question_id=?d", $eurid, $d)->max_arid;
        Database::get()->querySingle("DELETE FROM exercise_answer_record WHERE eurid=?d AND question_id=?d AND answer_record_id != ?d", $eurid, $d, $max_arid);
    }
    Session::Messages($langNewScoreRecorded, 'alert-success');
    if ($ajax_regrade) {
        echo json_encode(['result' => 'ok']);
        exit;
    } else {
        redirect_to_home_page("modules/exercise/exercise_result.php?course=$course_code&eurId=$eurid");
    }
}

if ($is_editor and ($totalScore != $exercise_user_record->total_score or $totalWeighting != $exercise_user_record->total_weighting)) {
    if ($checking) {
        echo json_encode(['result' => 'regrade', 'eurid' => $eurid,
            'title' => "$user->surname $user->givenname (" .
                       $exercise_user_record->record_start_date . ')',
            'url' => $urlAppend . "modules/exercise/exercise_result.php?course=$course_code&eurId=$eurid"],
            JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        Session::Messages($langScoreDiffers .
            "<form action='exercise_result.php?course=$course_code&amp;eurId=$eurid' method='post'>
                <button class='btn btn-default' type='submit' name='regrade' value='true'>$langRegrade</button>
             </form>", 'alert-warning');
    }
}

if ($checking) {
    echo json_encode(['result' => 'ok']);
    exit;
}

if ($showScore) {
    $tool_content .= "
    <br>
    <table class='table-default'>
        <tr>
            <td class='text-right'><b>$langYourTotalScore: <span id='total_score'>$exercise_user_record->total_score</span> / $exercise_user_record->total_weighting</b>
            </td>
        </tr>
    </table>";
}
$tool_content .= "  
  <div class='text-center'>";
    if ($is_editor && ($exercise_user_record->attempt_status == ATTEMPT_PENDING || $exercise_user_record->attempt_status == ATTEMPT_COMPLETED)) {
        $tool_content .= "<a class='btn btn-primary' href='index.php' id='submitButton'>$langSubmit</a>";
    }
    if (isset($_REQUEST['unit'])) {
        $tool_content .= "<a class='btn btn-default' href='../units/index.php?course=$course_code&id=$_REQUEST[unit]'>$langBack</a>";
    } else {
        $tool_content .= "<a class='btn btn-default' href='index.php?course=$course_code'>$langBack</a>";
    }

$tool_content .= "</div>";

draw($tool_content, 2, null, $head_content);
