<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Calendar</title>
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


        h1 {
            margin: 20px 0;
            font-size: 28px;
            color: black;
            text-align: center;
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
            position: relative;
        }

        .days .today {
            background: #2196f3;
            color: white;
            font-weight: bold;
            transform: scale(1.1);
        }

        .days .holiday {
            background: #ffebee;
            color: red;
        }

        .days .event-day {
            background: #e8f5e9;
            color: #4caf50;
        }

        .days .event-holiday {
            background: linear-gradient(135deg, #e8f5e9 50%, #ffebee 50%);
            color: #000;
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
            max-height: 300px;
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

        .events-box li,
        .holidays-box li {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 14px;
        }

        .events-box li {
            background: #e8f5e9;
            color: #4caf50;
        }

        .holidays-box li {
            color: red;
            background-color: #ffebee;
        }

        /* Event indicator */
        .event-indicator {
            position: absolute;
            bottom: 5px;
            width: 80%;
            height: 4px;
            border-radius: 2px;

        }

        .holiday-indicator {
            position: absolute;
            bottom: 5px;
            width: 80%;
            height: 4px;
            border-radius: 2px;

        }

        .both-indicator {
            position: absolute;
            bottom: 5px;
            width: 80%;
            height: 4px;
            border-radius: 2px;
            background: linear-gradient(90deg, #4caf50 50%, #f44336 50%);
        }

        @media (max-width: 768px) {
            .events-container {
                grid-template-columns: 1fr;
            }

            .calendar-container {
                max-width: 100%;
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

        // Load from localStorage or use defaults
        let holidays = JSON.parse(localStorage.getItem('holidays')) || {
            "01-01": {
                name: "New Year's Day",
                type: "recurring"
            },
            "02-14": {
                name: "Valentine's Day",
                type: "recurring"
            },
            "03-17": {
                name: "St. Patrick's Day",
                type: "recurring"
            },
            "07-04": {
                name: "Independence Day",
                type: "recurring"
            },
            "10-31": {
                name: "Halloween",
                type: "recurring"
            },
            "11-23": {
                name: "Thanksgiving",
                type: "recurring"
            },
            "12-25": {
                name: "Christmas Day",
                type: "recurring"
            },
            "12-31": {
                name: "New Year's Eve",
                type: "recurring"
            }
        };

        let events = JSON.parse(localStorage.getItem('events')) || {
            "01-15": {
                name: "Spring Semester Begins",
                type: "recurring"
            },
            "03-01": {
                name: "Mid-Term Week Begins",
                type: "recurring"
            },
            "03-15": {
                name: "Spring Break Starts",
                type: "recurring"
            },
            "05-01": {
                name: "Final Exams Begin",
                type: "recurring"
            },
            "05-15": {
                name: "Graduation Ceremony",
                type: "recurring"
            },
            "08-25": {
                name: "Fall Semester Begins",
                type: "recurring"
            },
            "10-15": {
                name: "Mid-Term Week Begins",
                type: "recurring"
            },
            "12-10": {
                name: "Final Exams Begin",
                type: "recurring"
            },
            "12-20": {
                name: "Winter Break Starts",
                type: "recurring"
            }
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
                const fullDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Check if there's an event or holiday for this date
                const hasEvent = checkDateForEvents(dateStr, fullDateStr);
                const hasHoliday = checkDateForHolidays(dateStr, fullDateStr);

                // Calculate the day of week (0-6, where 0 is Sunday)
                const dayOfWeek = (firstDay + day - 1) % 7;

                // Add appropriate classes
                if (hasEvent && hasHoliday) {
                    dayEl.classList.add("event-holiday");
                    // Add indicator for both
                    const indicator = document.createElement("div");
                    indicator.className = "both-indicator";
                    dayEl.appendChild(indicator);
                } else if (hasEvent) {
                    dayEl.classList.add("event-day");
                    // Add green indicator
                    const indicator = document.createElement("div");
                    indicator.className = "event-indicator";
                    dayEl.appendChild(indicator);
                } else if (hasHoliday || dayOfWeek === 0) { // Sunday
                    dayEl.classList.add("holiday");
                    // Add red indicator
                    const indicator = document.createElement("div");
                    indicator.className = "holiday-indicator";
                    dayEl.appendChild(indicator);
                }

                // Today's date
                const today = new Date();
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayEl.classList.add("today");
                }

                daysEl.appendChild(dayEl);
            }

            // Update lists
            updateLists(month, year);
        }

        function checkDateForEvents(dateStr, fullDateStr) {
            // Check recurring events (MM-DD)
            if (events[dateStr] && events[dateStr].type === "recurring") {
                return true;
            }

            // Check single-day events (YYYY-MM-DD)
            if (events[fullDateStr] && events[fullDateStr].type === "single") {
                return true;
            }

            // Check range events
            for (const key in events) {
                if (events[key].type === "range") {
                    const [startDate, endDate] = key.split("_to_");
                    const currentDate = new Date(fullDateStr);
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    if (currentDate >= start && currentDate <= end) {
                        return true;
                    }
                }
            }

            return false;
        }

        function checkDateForHolidays(dateStr, fullDateStr) {
            // Check recurring holidays (MM-DD)
            if (holidays[dateStr] && holidays[dateStr].type === "recurring") {
                return true;
            }

            // Check single-day holidays (YYYY-MM-DD)
            if (holidays[fullDateStr] && holidays[fullDateStr].type === "single") {
                return true;
            }

            // Check range holidays
            for (const key in holidays) {
                if (holidays[key].type === "range") {
                    const [startDate, endDate] = key.split("_to_");
                    const currentDate = new Date(fullDateStr);
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    if (currentDate >= start && currentDate <= end) {
                        return true;
                    }
                }
            }

            return false;
        }

        function formatDateForDisplay(date) {
            return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
        }

        function updateLists(month, year) {
            holidaysListEl.innerHTML = '';
            eventsListEl.innerHTML = '';

            // Display holidays for current month
            for (const key in holidays) {
                let displayHoliday = false;
                let displayDate = "";

                if (holidays[key].type === "recurring") {
                    const [m, d] = key.split('-');
                    if (parseInt(m) === month + 1) {
                        displayHoliday = true;
                        displayDate = `${monthNames[month]} ${parseInt(d)}`;
                    }
                } else if (holidays[key].type === "single") {
                    const date = new Date(key);
                    if (date.getMonth() === month && date.getFullYear() === year) {
                        displayHoliday = true;
                        displayDate = `${monthNames[month]} ${date.getDate()}, ${year}`;
                    }
                } else if (holidays[key].type === "range") {
                    const [startDate, endDate] = key.split("_to_");
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    // Display if any part of the range falls in current month
                    const startOfMonth = new Date(year, month, 1);
                    const endOfMonth = new Date(year, month + 1, 0);

                    if ((start <= endOfMonth && end >= startOfMonth)) {
                        displayHoliday = true;
                        displayDate = `${formatDateForDisplay(start)} to ${formatDateForDisplay(end)}`;
                    }
                }

                if (displayHoliday) {
                    addListItem(holidaysListEl, holidays[key].name, displayDate);
                }
            }

            // Display events for current month
            for (const key in events) {
                let displayEvent = false;
                let displayDate = "";

                if (events[key].type === "recurring") {
                    const [m, d] = key.split('-');
                    if (parseInt(m) === month + 1) {
                        displayEvent = true;
                        displayDate = `${monthNames[month]} ${parseInt(d)}`;
                    }
                } else if (events[key].type === "single") {
                    const date = new Date(key);
                    if (date.getMonth() === month && date.getFullYear() === year) {
                        displayEvent = true;
                        displayDate = `${monthNames[month]} ${date.getDate()}, ${year}`;
                    }
                } else if (events[key].type === "range") {
                    const [startDate, endDate] = key.split("_to_");
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    // Display if any part of the range falls in current month
                    const startOfMonth = new Date(year, month, 1);
                    const endOfMonth = new Date(year, month + 1, 0);

                    if ((start <= endOfMonth && end >= startOfMonth)) {
                        displayEvent = true;
                        displayDate = `${formatDateForDisplay(start)} to ${formatDateForDisplay(end)}`;
                    }
                }

                if (displayEvent) {
                    addListItem(eventsListEl, events[key].name, displayDate);
                }
            }
        }

        function addListItem(listEl, name, dateText) {
            const li = document.createElement("li");
            li.textContent = `${dateText}: ${name}`;
            listEl.appendChild(li);
        }

        // Event Listeners
        prevMonthBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextMonthBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        // Initialize
        renderCalendar();
    </script>
</body>

</html>