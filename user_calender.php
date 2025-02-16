<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="user.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .main-box {
            display: flex;
            width: 100%;
            column-gap: 5%;
            overflow-x: hidden;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, white, grey);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .content {
            flex: 5;
            max-width: 100%;
            padding-right: 20px;
            overflow-x: hidden;
        }

        aside {
            flex: 1.35;
        }

        h1 {
            margin: 20px 0;
            font-size: 28px;
            color: black;
        }

        .calendar-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
            max-width: 100%;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgb(50, 147, 94);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .calendar-header button {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .calendar-body {
            display: grid;
            grid-template-rows: auto 1fr;
            padding: 20px;
        }

        .day-names {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            font-weight: bold;
            text-align: center;
            color: #555;
            margin-bottom: 10px;
        }

        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-auto-rows: minmax(60px, auto);
            gap: 5px;
        }

        .days span {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .days span:hover {
            background: #2196f3;
            color: white;
        }

        .days .today {
            background: #2196f3;
            color: white;
            font-weight: bold;
            transform: scale(1.1);
        }

        .days .holiday {
            background: #ff5722;
            color: white;
        }

        .days .event-day {
            background: #4caf50;
            color: white;
        }

        .events-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
            max-width: 100%;
            margin: 20px 0;
        }

        .events-box,
        .holidays-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            height: auto;
            max-height: calc(100vh - 600px);
            min-height: 200px;
            overflow-y: auto;
        }

        .events-box h2,
        .holidays-box h2 {
            color: #2196f3;
            font-size: 20px;
            margin-bottom: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            background: white;
            padding: 10px 0;
        }

        .events-box ul,
        .holidays-box ul {
            list-style: none;
            padding: 0;
        }

        .events-box li {
            background: #e8f5e9;
            color: #4caf50;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 14px;
            transition: transform 0.2s ease;
        }


        /* Add to your existing CSS */
        .days span[style*="color: red"] {
            background-color: #ffebee;
            /* Light red background */
        }

        .days span[style*="color: red"]:hover {
            background-color: #ff5722;
            /* Darker red on hover */
            color: white !important;
            /* White text on hover */
        }

        /* Add or update in your CSS */
        .days span.holiday {
            color: red !important;
            background-color: #ffebee !important;
        }

        .days span.holiday:hover {
            background-color: #ff5722 !important;
            color: white !important;
        }

        /* Make sure holiday status doesn't get overridden */
        .days .today.holiday {
            color: red !important;
            border: 2px solid #ff5722;
        }

        .holidays-box li {
            color: red;
            background-color: #ffebee;
        }

        .events-box li:hover,
        .holidays-box li:hover {
            transform: translateX(5px);
        }

        @media (max-width: 900px) {
            .main-box {
                flex-direction: column;
                padding: 15px;
            }

            .events-container {
                grid-template-columns: 1fr;
            }

            .events-box,
            .holidays-box {
                max-height: 300px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="main-box">
        <?php include 'user_sidebar.php'; ?>
        <script>
      document.querySelector('a[href="user_calender.php"]').classList.add('active-page');
    </script>
        <div class="content">
            <h1>Calendar</h1>
            <div class="calendar-container">
                <div class="calendar-header">
                    <button id="prev-month">❮</button>
                    <h2 id="month-year"></h2>
                    <button id="next-month">❯</button>
                </div>
                <div class="calendar-body">
                    <div class="day-names">
                        <span>Sun</span><span>Mon</span><span>Tue</span>
                        <span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                    </div>
                    <div class="days" id="days"></div>
                </div>
            </div>
            <div class="events-container">
                <div class="holidays-box">
                    <h2>Holidays</h2>
                    <ul id="holidays-list"></ul>
                </div>
                <div class="events-box">
                    <h2>Events</h2>
                    <ul id="events-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        const monthYearEl = document.getElementById("month-year");
        const daysEl = document.getElementById("days");
        const holidaysListEl = document.getElementById("holidays-list");
        const eventsListEl = document.getElementById("events-list");
        const prevMonthBtn = document.getElementById("prev-month");
        const nextMonthBtn = document.getElementById("next-month");

        let currentDate = new Date();

        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        const holidays = {
            "01-01": "New Year's Day",
            "02-14": "Valentine's Day",
            "03-17": "St. Patrick's Day",
            "07-04": "Independence Day",
            "10-31": "Halloween",
            "11-23": "Thanksgiving",
            "12-25": "Christmas Day",
            "12-31": "New Year's Eve"
        };

        const events = {
            "01-15": "Spring Semester Begins",
            "03-01": "Mid-Term Week Begins",
            "03-15": "Spring Break Starts",
            "05-01": "Final Exams Begin",
            "05-15": "Graduation Ceremony",
            "08-25": "Fall Semester Begins",
            "10-15": "Mid-Term Week Begins",
            "12-10": "Final Exams Begin",
            "12-20": "Winter Break Starts"
        };

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthYearEl.textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            daysEl.innerHTML = '';

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement("span");
                daysEl.appendChild(emptyDay);
            }

            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayEl = document.createElement("span");
                dayEl.textContent = day;

                const dateStr = `${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Calculate the day of week (0-6, where 0 is Sunday)
                const dayOfWeek = (firstDay + day - 1) % 7;

                // Check if it's a Saturday (6) or a holiday
                if (dayOfWeek === 6 || holidays[dateStr]) {
                    dayEl.style.color = "red";
                    dayEl.style.backgroundColor = "#ffebee";
                    dayEl.classList.add("holiday");
                }

                if (events[dateStr]) {
                    dayEl.classList.add("event-day");
                }

                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayEl.classList.add("today");
                }

                daysEl.appendChild(dayEl);
            }

            // Update lists
            updateLists(month);
        }

        function updateLists(month) {
            holidaysListEl.innerHTML = '';
            eventsListEl.innerHTML = '';

            Object.entries(holidays).forEach(([date, name]) => {
                const [m, d] = date.split('-');
                if (parseInt(m) === month + 1) {
                    const li = document.createElement("li");
                    li.textContent = `${monthNames[month]} ${parseInt(d)}: ${name}`;
                    holidaysListEl.appendChild(li);
                }
            });

            Object.entries(events).forEach(([date, name]) => {
                const [m, d] = date.split('-');
                if (parseInt(m) === month + 1) {
                    const li = document.createElement("li");
                    li.textContent = `${monthNames[month]} ${parseInt(d)}: ${name}`;
                    eventsListEl.appendChild(li);
                }
            });
        }

        prevMonthBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextMonthBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();
    </script>
</body>

</html>