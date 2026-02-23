<!DOCTYPE html>
<html>
<head>
    <title>Vulnerable Search</title>
</head>
<body>
    <h2>Vulnerable Search Page</h2>

    <form method="GET" action="/vulnerable/search">
        <input type="text" name="q" placeholder="Search...">
        <button type="submit">Search</button>
    </form>

    <p>Result: {!! $query !!}</p> <!-- VULNERABLE -->
</body>
</html>