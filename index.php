<!DOCTYPE html>
<html lang="si">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>නව බ්ලොග් ලිපියක් එක් කරන්න</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
/* Form එකේ කොටස් ලස්සන කරන්න මේ ටික එකතු කළා */
.form-label {
display: block; /* ලේබලය අලුත් රේඛාවකට ගන්න */
font-size: 0.875rem; /* පොඩි අකුරු */
font-weight: 500; /* තද අකුරු */
color: #4b5563; /* අළු පාට */
margin-bottom: 0.25rem; /* ලේබලයට යටින් පොඩි ඉඩක් */
}
.form-input {
margin-top: 0.25rem; /* input එකට උඩින් පොඩි ඉඩක් */
display: block; /* input එක අලුත් රේඛාවකට ගන්න */
width: 100%; /* සම්පූර්ණ පළල ගන්න */
padding: 0.5rem 0.75rem; /* ඇතුළත ඉඩ */
border: 1px solid #d1d5db; /* දේශසීමා පාට */
border-radius: 0.375rem; /* දේශසීමා වටකුරු කරන්න */
box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* පොඩි ෂැඩෝ එකක් */
outline: none; /* click කළාම එන outline එක අයින් කරන්න */
/* focus කළාම පාට වෙනස් කරන්න */
transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.form-input:focus {
border-color: #6366f1; /* focus කළාම දේශසීමා indigo පාට කරන්න */
box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25); /* focus කළාම ෂැඩෝ එකක් */
}
.form-textarea {
@apply form-input; /* input එකට දුන්නු styles මේකටත් දෙන්න */
height: 12rem; /* උස වැඩි කරන්න (අකුරු 400ක් දාන්න) */
}
</style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
<h2 class="text-2xl font-bold text-center text-gray-800 mb-6">නව බ්ලොග් ලිපියක් එක් කරන්න</h2>
<form action="process_post.php" method="POST" enctype="multipart/form-data">
<div class="mb-4">
<label for="article_name" class="form-label">ලිපියේ නම:</label>
<input type="text" id="article_name" name="article_name" class="form-input" required>
</div>

<div class="mb-4">
<label for="article_image" class="form-label">ලිපියේ පින්තූරය (Featured Image):</label>
<input type="file" id="article_image" name="article_image" accept="image/*" class="form-input p-1" required>
</div>

<div class="mb-4">
<label for="author_name" class="form-label">කර්තෘගේ නම:</label>
<input type="text" id="author_name" name="author_name" class="form-input" required>
</div>

<div class="mb-6">
<label for="article_content" class="form-label">ලිපියේ අන්තර්ගතය (අවම 400 අකුරු):</label>
<textarea id="article_content" name="article_content" class="form-textarea" required minlength="400"></textarea>
</div>

<button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
ලිපිය පළ කරන්න
</button>
</form>
</div>
</body>
</html>