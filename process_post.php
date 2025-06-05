<?php
session_start(); // Session ආරම්භ කරන්න, දෝෂ ගබඩා කිරීමට මෙය අවශ්‍යයි

// දත්ත පිරිසිදු කරන function එක (Security සඳහා වැදගත්)
function validate_input($data) {
    $data = trim($data); // ඉස්සරහින් සහ පස්සෙන් තියෙන හිස්තැන් අයින් කරනවා
    $data = stripslashes($data); // Backslashes (\) අයින් කරනවා
    $data = htmlspecialchars($data); // HTML symbols (<, >, &) වෙනත් code බවට පරිවර්තනය කරනවා (hacking වලින් ආරක්ෂාවට)
    return $data;
}

// මෙම ගොනුවට POST ඉල්ලීමක් ලැබී ඇත්දැයි පරීක්ෂා කරන්න
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // දෝෂ ගබඩා කිරීමට හිස් array එකක් සාදන්න
    $errors = [];

    // POST දත්ත ලබාගෙන validate_input ශ්‍රිතය භාවිතයෙන් පිරිසිදු කරන්න
    $article_name = validate_input($_POST['article_name'] ?? '');
    $author_name = validate_input($_POST['author_name'] ?? '');
    $article_content = validate_input($_POST['article_content'] ?? '');

    // පෙර ඇතුලත් කල දත්ත session එකේ තබා ගන්න (දෝෂ ඇති වුවහොත් form එකේ පෙන්වීමට)
    $_SESSION['old_inputs'] = [
        'article_name' => $article_name,
        'author_name' => $author_name,
        'article_content' => $article_content,
    ];

    $image_path = ''; // මුලින්ම image_path එක හිස් string එකක් ලෙස සකසන්න

    // Image Upload Handling
    if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] == 0) {
        $upload_dir = 'uploads/'; // ඔබගේ images save කරන folder එක

        // uploads ෆෝල්ඩරය නොමැති නම් සාදන්න
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // ෆෝල්ඩරය නොමැති නම් සාදන්න, සහ permissions දෙන්න
        }

        $image_name = basename($_FILES['article_image']['name']);
        // ගොනුවට unique නමක් දෙන්න, නැතිනම් එකම නමේ ගොනු overwrite විය හැක
        $target_file = $upload_dir . uniqid() . '_' . $image_name;

        // අවසර ලත් file වර්ග පරීක්ෂා කරන්න
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "png", "jpeg", "gif");

        // ගොනු ප්‍රමාණය පරීක්ෂා කරන්න (උදා: 5MB ට අඩු විය යුතුය)
        if ($_FILES['article_image']['size'] > 5 * 1024 * 1024) { // 5MB
            $errors[] = "පින්තූරය විශාල වැඩියි. උපරිම ප්‍රමාණය 5MB.";
        }

        if (in_array($imageFileType, $allowed_types)) {
            // පින්තූරය server එකට move කරන්න
            if (move_uploaded_file($_FILES['article_image']['tmp_name'], $target_file)) {
                $image_path = $target_file; // සාර්ථක නම් image_path එක සකසන්න
            } else {
                $errors[] = "පින්තූරය upload කිරීමේදී දෝෂයක් සිදුවිය. Folder permissions පරීක්ෂා කරන්න.";
            }
        } else {
            $errors[] = "අවසර ලත් file වර්ග (JPG, JPEG, PNG, GIF) පමණක් upload කරන්න.";
        }
    }


    // දත්ත වලංගු කිරීම (Validation)
    if (empty($article_name)) {
        $errors[] = "ලිපියේ නම ඇතුළත් කරන්න.";
    }
    if (empty($author_name)) {
        $errors[] = "කර්තෘගේ නම ඇතුළත් කරන්න.";
    }
    if (empty($article_content)) {
        $errors[] = "ලිපියේ අන්තර්ගතය ඇතුළත් කරන්න.";
    } elseif (strlen($article_content) < 400) {
        $errors[] = "ලිපියේ අන්තර්ගතය අවම වශයෙන් අක්ෂර 400ක් විය යුතුය. (දැනට: " . strlen($article_content) . " අක්ෂර).";
    }

    // දෝෂ නොමැති නම් ලිපිය ප්‍රදර්ශනය කරන්න
    if (empty($errors)) {
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif; /* Poppins font එක apply කරන්න */
        }
        .article-content {
            white-space: pre-wrap; /* text area එකෙන් දාපු අලුත් රේඛා හරිහැටි පෙන්නන්න */
            line-height: 1.8; /* කියවීමට පහසු වන පරිදි පේළි අතර පරතරය වැඩි කරන්න */
            text-align: justify; /* දෙපසට align කරන්න */
            font-size: 1.125rem; /* text-lg ට වඩා ටිකක් වැඩි කරන්න */
            color: #333; /* තද අළු පැහැයක් දෙන්න */
        }
        /* Subheadings (h2, h3) සඳහා styles */
        .article-content h2 {
            font-size: 1.875rem; /* text-3xl */
            font-weight: 700; /* font-bold */
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1a202c; /* gray-900 */
        }
        .article-content h3 {
            font-size: 1.5rem; /* text-2xl */
            font-weight: 600; /* font-semibold */
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #2d3748; /* gray-800 */
        }
        /* Paragraphs (p) අතර පරතරය */
        .article-content p {
            margin-bottom: 1rem;
        }
        /* Lists (ul, ol) සඳහා styles */
        .article-content ul, .article-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .article-content ul li {
            list-style-type: disc;
        }
        .article-content ol li {
            list-style-type: decimal;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-10 rounded-xl shadow-2xl w-full max-w-4xl border border-gray-200">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-6 text-center leading-tight">
            <?php echo htmlspecialchars($article_name); ?>
        </h1>
        <?php if (!empty($image_path) && file_exists($image_path)): // පින්තූරය තිබේ නම් සහ පවතිනවා නම් පමණක් පෙන්වන්න ?>
            <div class="mb-8 flex justify-center">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($article_name); ?>" class="w-full h-auto max-h-96 object-cover rounded-xl shadow-lg border border-gray-300">
            </div>
        <?php endif; ?>
        <p class="text-gray-500 text-base mb-6 text-center italic">
            කර්තෘ: <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($author_name); ?></span> |
            පළ කළ දිනය: <?php echo date("Y-m-d, H:i"); ?>
        </p>
        <div class="article-content text-gray-800">
            <?php echo nl2br(htmlspecialchars($article_content)); ?>
        </div>
        <div class="mt-10 text-center">
            <a href="index.php" class="inline-block bg-indigo-700 text-white py-3 px-8 rounded-full hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                තවත් ලිපියක් එක් කරන්න
            </a>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        // දෝෂ තිබේ නම්, ඒවා session එකේ ගබඩා කර index.php වෙත යොමු කරන්න
        $_SESSION['errors'] = $errors;
        header("Location: index.php");
        exit();
    }
} else {
    // direct මේ file එකට ආවොත්, Form එකට යවන්න (index.php)
    header("Location: index.php");
    exit();
}
?>