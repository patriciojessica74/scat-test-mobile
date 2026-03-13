<?php 
// 1. Include the config at the very top
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="script-src 'self'; object-src 'none';">
    <title>Sign Up | SCAT CMS</title>
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class="container">
    <div class="left-panel">
        <div class="overlay">
            <img src="<?php echo IMG_PATH; ?>scatlogo.png" alt="Logo" class="logo">
            <div class="logo-text">SMART CARD-BASED ATTENDANCE SYSTEM</div>
        </div>
    </div>

    <div class="right-panel">
        <h1>Create your account</h1>
        <p>Already have an account? <a href="<?php echo BASE_URL; ?>login.php" style="color:#003cff; font-weight:bold;">Log in here!</a></p>

        <form action="<?php echo FUNC_URL; ?>register-function.php" method="POST" id="signupForm">
            <input type="hidden" name="usertype" value="student">
            <input type="hidden" name="source" value="user">

            <div class="input-grid">
                <div class="col">
                    <div class="input-group">
                        <label for="username">USERNAME (e.g., JOHN123)</label>
                        <input type="text" id="username" name="username" placeholder=" JOHN123" autocomplete="username" required>
                    </div>
                    <div class="input-group">
                        <label for="email">EMAIL (e.g., email@address.com)</label>
                        <input type="email" id="email" name="email" placeholder=" johndelacruz@gmail.com" autocomplete="email" required>
                    </div>
                    <div class="input-group">
                        <label for="fullname">FULL NAME (FIRST NAME, SURNAME)</label>
                        <input type="text" id="fullname" name="full_name" placeholder=" JOHN DELACRUZ" autocomplete="name" required>
                    </div>
                    <div class="input-group">
                        <label for="studentid">STUDENT ID (e.g., A202612345)</label>
                        <input type="text" id="studentid" name="student_id" placeholder=" A202612345" maxlength="10" minlength="10" pattern="A[0-9]{9}" required>
                        <span id="studentid-warning">
                        Must start with A + 9 digits (e.g., A202612345)
                        </span>
                    </div>
                </div>

                <div class="col">
                     <div class="input-group">
                        <label for="program">PROGRAM</label>
                        <select id="program" name="program" required>
                            <option value="" disabled selected>SELECT PROGRAM</option>
                            <option value=""> BS COMPUTER SCIENCE</option>
                        </select>
                    </div>
                    <div class="row-flex">
                        <div class="input-group" style="flex:1;">
                            <label for="year">YEAR LEVEL</label>
                            <select id="year" name="year" required>
                                <option value="" disabled selected >YEAR LEVEL</option>
                                <option value=""> 4 </option>
                            </select>
                        </div>
                        <div class="input-group" style="flex:1;">
                            <label for="section">SECTION</label>
                            <select id="section" name="section" required>
                                <option value="" disabled selected >SECTION</option>
                                <option value="">1</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" id="password" name="password" minlength="8" 
                        placeholder="CREATE PASSWORD" autocomplete="new-password" required>
                    </div>

                    <div class="password-help">
                        <span id="toggleRequirements">ⓘ Password requirements</span>

                        <div id="requirementsBox" class="requirements-box">
                            <span id="closeRequirements" class="close-btn">✕</span>

                            <ul>
                                <li id="rule-length">✖ At least 8 characters</li>
                                <li id="rule-lower">✖ One lowercase letter</li>
                                <li id="rule-upper">✖ One uppercase letter</li>
                                <li id="rule-number">✖ One number</li>
                                <li id="rule-special">✖ One special character (@$!%*?&)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirmPassword">CONFIRM PASSWORD</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="RE-ENTER PASSWORD" autocomplete="new-password" required>
                    </div>
                    <span id="password-error" style="color:red; display:none; font-size:11px; margin:6px 0;">
                        Passwords do not match
                    </span>
                    <div class="show-password">
                        <input type="checkbox" id="showPassword">
                        <label for="showPassword">SHOW PASSWORD</label>
                    </div>
                </div>
            </div>

            <div class="status-divider">
                <span>SELECT YOUR STATUS</span>
            </div>
            <div class="status-grid">
                <label class="status-card">
                    <input type="radio" name="status" value="REGULAR">
                    <div class="card-content">
                        <div class="text-info">REGULAR STUDENT</div>
                    </div>
                </label>

                <label class="status-card">
                    <input type="radio" name="status" value="IRREGULAR">
                    <div class="card-content">
                        <div class="text-info">IRREGULAR STUDENT</div>
                    </div>
                </label>

                <label class="status-card">
                    <input type="radio" name="status" value="RETURNEE">
                    <div class="card-content">
                        <div class="text-info">RETURNEE STUDENT</div>
                    </div>
                </label>
            </div>

            <div class="center">
                <button type="submit" class="register-btn"> REGISTER</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo JS_PATH; ?>signup.js" defer></script>
</body>
</html>