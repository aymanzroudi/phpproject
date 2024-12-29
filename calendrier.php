<?php
session_start();
require_once 'config/database.php';

// Récupérer tous les événements pour le calendrier
$stmt = $pdo->query("SELECT id, title, date, category FROM events ORDER BY date ASC");
$events = $stmt->fetchAll();

// Formater les événements pour le calendrier
$calendarEvents = array();
foreach ($events as $event) {
    $calendarEvents[] = array(
        'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['date'],
        'className' => 'event-' . strtolower($event['category'])
    );
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - SportEvents</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="calendar-page">
        <div class="calendar-container">
            <h1>Calendrier des Événements</h1>
            <div id="calendar"></div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo json_encode($calendarEvents); ?>,
                eventClick: function(info) {
                    window.location.href = 'evenements.php?id=' + info.event.id;
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
