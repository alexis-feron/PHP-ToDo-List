<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter une tâche</title>
</head>
<body>
    <?php require("barreNav.php") ?>
    <form method="post" action="?action=ajouteLaTache&liste=<?= $_REQUEST["liste"]?>">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="nomNvTache"></td>
                </tr>
            </tbody>
        </table>
        <input value="Annuler" type="reset">
        <input value="Ajouter" type="submit">
    </form>
</body>
</html>