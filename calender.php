<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="admin.css">
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
            position: relative;
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

        .events-box li,
        .holidays-box li {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 14px;
            transition: transform 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .events-box li {
            background: #e8f5e9;
            color: #4caf50;
        }

        .holidays-box li {
            color: red;
            background-color: #ffebee;
        }

        .events-box li:hover,
        .holidays-box li:hover {
            transform: translateX(5px);
        }

        .list-actions {
            display: flex;
            gap: 5px;
        }

        .list-actions button {
            border: none;
            background: none;
            cursor: pointer;
            padding: 3px;
            border-radius: 3px;
            font-size: 12px;
        }

        .edit-btn {
            color: #2196f3;
        }

        .delete-btn {
            color: #f44336;
        }

        .add-btn {
            background: #4caf50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
            transition: background-color 0.3s;
        }

        .add-btn:hover {
            background: #388e3c;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal h3 {
            margin-top: 0;
            color: #333;
            margin-bottom: 20px;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .date-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .form-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cancel-btn {
            background-color: #f5f5f5;
            color: #333;
        }

        .save-btn {
            background-color: #4caf50;
            color: white;
        }

        .delete-btn:hover {
            background-color: #d32f2f;
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
    </style>
</head>


<body>
    <div class="main-box">
        <?php include 'sidebar.php'; ?>
        <script>
      document.querySelector('a[href="calender.php"]').classList.add('active-page');
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
                    <button id="add-holiday-btn" class="add-btn">+ Add Holiday</button>
                    <ul id="holidays-list"></ul>
                </div>
                <div class="events-box">
                    <h2>Events</h2>
                    <button id="add-event-btn" class="add-btn">+ Add Event</button>
                    <ul id="events-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="modal">
        <div class="modal-content">
            <h3 id="event-modal-title">Add Event</h3>
            <form id="event-form" class="modal-form">
                <input type="hidden" id="edit-id">
                <input type="hidden" id="edit-type">
                
                <div class="form-group">
                    <label for="event-name">Name:</label>
                    <input type="text" id="event-name" required>
                </div>
                
                <div class="form-group">
                    <label for="event-type">Date Type:</label>
                    <select id="event-type">
                        <option value="single">Single Day</option>
                        <option value="range">Date Range</option>
                        <option value="recurring">Recurring (Annual)</option>
                    </select>
                </div>
                
                <div class="form-group" id="single-date-group">
                    <label for="event-date">Date:</label>
                    <input type="date" id="event-date">
                </div>
                
                <div class="form-group" id="date-range-group" style="display: none;">
                    <label>Date Range:</label>
                    <div class="date-range">
                        <input type="date" id="event-start-date" placeholder="Start Date">
                        <span>to</span>
                        <input type="date" id="event-end-date" placeholder="End Date">
                    </div>
                </div>
                
                <div class="form-group" id="recurring-date-group" style="display: none;">
                    <label for="recurring-month">Month:</label>
                    <select id="recurring-month">
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    
                    <label for="recurring-day">Day:</label>
                    <input type="number" id="recurring-day" min="1" max="31" value="1">
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="cancel-btn" id="close-modal">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const monthYearEl = document.getElementById("month-year");
    const daysEl = document.getElementById("days");
    const holidaysListEl = document.getElementById("holidays-list");
    const eventsListEl = document.getElementById("events-list");
    const prevMonthBtn = document.getElementById("prev-month");
    const nextMonthBtn = document.getElementById("next-month");
    const addEventBtn = document.getElementById("add-event-btn");
    const addHolidayBtn = document.getElementById("add-holiday-btn");
    const eventModal = document.getElementById("event-modal");
    const eventForm = document.getElementById("event-form");
    const closeModalBtn = document.getElementById("close-modal");
    const eventModalTitle = document.getElementById("event-modal-title");
    const eventTypeSelect = document.getElementById("event-type");
    const singleDateGroup = document.getElementById("single-date-group");
    const dateRangeGroup = document.getElementById("date-range-group");
    const recurringDateGroup = document.getElementById("recurring-date-group");

    let currentDate = new Date();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Load from localStorage or use defaults
    let holidays = JSON.parse(localStorage.getItem('holidays')) || {
        "01-01": { name: "New Year's Day", type: "recurring" },
        "02-14": { name: "Valentine's Day", type: "recurring" },
        "03-17": { name: "St. Patrick's Day", type: "recurring" },
        "07-04": { name: "Independence Day", type: "recurring" },
        "10-31": { name: "Halloween", type: "recurring" },
        "11-23": { name: "Thanksgiving", type: "recurring" },
        "12-25": { name: "Christmas Day", type: "recurring" },
        "12-31": { name: "New Year's Eve", type: "recurring" }
    };

    let events = JSON.parse(localStorage.getItem('events')) || {
        "01-15": { name: "Spring Semester Begins", type: "recurring" },
        "03-01": { name: "Mid-Term Week Begins", type: "recurring" },
        "03-15": { name: "Spring Break Starts", type: "recurring" },
        "05-01": { name: "Final Exams Begin", type: "recurring" },
        "05-15": { name: "Graduation Ceremony", type: "recurring" },
        "08-25": { name: "Fall Semester Begins", type: "recurring" },
        "10-15": { name: "Mid-Term Week Begins", type: "recurring" },
        "12-10": { name: "Final Exams Begin", type: "recurring" },
        "12-20": { name: "Winter Break Starts", type: "recurring" }
    };

    // Save data to localStorage
    function saveData() {
        localStorage.setItem('holidays', JSON.stringify(holidays));
        localStorage.setItem('events', JSON.stringify(events));
    }

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

            // Add click event for adding new entry
            dayEl.addEventListener("click", () => {
                const clickDate = new Date(year, month, day);
                handleDayClick(clickDate);
            });

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

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function handleDayClick(date) {
        // Open modal for adding new event/holiday
        document.getElementById("event-date").value = formatDate(date);
        document.getElementById("event-start-date").value = formatDate(date);
        document.getElementById("event-end-date").value = formatDate(date);
        document.getElementById("recurring-month").value = String(date.getMonth() + 1).padStart(2, '0');
        document.getElementById("recurring-day").value = date.getDate();
        
        // Show modal for adding event
        eventModalTitle.textContent = "Add Event";
        document.getElementById("edit-type").value = "event";
        document.getElementById("edit-id").value = "";
        openModal();
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
                addListItem(holidaysListEl, key, holidays[key].name, displayDate, "holiday");
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
                addListItem(eventsListEl, key, events[key].name, displayDate, "event");
            }
        }
    }

    function formatDateForDisplay(date) {
        return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }

    function addListItem(listEl, id, name, dateText, type) {
        const li = document.createElement("li");
        
        const textSpan = document.createElement("span");
        textSpan.textContent = `${dateText}: ${name}`;
        li.appendChild(textSpan);
        
        const actions = document.createElement("div");
        actions.className = "list-actions";
        
        const editBtn = document.createElement("button");
        editBtn.className = "edit-btn";
        editBtn.innerHTML = "✎";
        editBtn.title = "Edit";
        editBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            editItem(id, type);
        });
        
        const deleteBtn = document.createElement("button");
        deleteBtn.className = "delete-btn";
        deleteBtn.innerHTML = "×";
        deleteBtn.title = "Delete";
        deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            deleteItem(id, type);
        });
        
        actions.appendChild(editBtn);
        actions.appendChild(deleteBtn);
        li.appendChild(actions);
        
        listEl.appendChild(li);
    }

    function editItem(id, type) {
        let dataSource = type === "holiday" ? holidays : events;
        let data = dataSource[id];
        
        if (!data) return;
        
        // Set form values
        document.getElementById("event-name").value = data.name;
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-type").value = type;
        
        // Set date type and show appropriate fields
        document.getElementById("event-type").value = data.type;
        updateDateFields();
        
        if (data.type === "recurring") {
            const [month, day] = id.split('-');
            document.getElementById("recurring-month").value = month;
            document.getElementById("recurring-day").value = parseInt(day);
        } else if (data.type === "single") {
            document.getElementById("event-date").value = id;
        } else if (data.type === "range") {
            const [startDate, endDate] = id.split("_to_");
            document.getElementById("event-start-date").value = startDate;
            document.getElementById("event-end-date").value = endDate;
        }
        
        // Set modal title
        eventModalTitle.textContent = type === "holiday" ? "Edit Holiday" : "Edit Event";
        
        // Open modal
        openModal();
    }

    function deleteItem(id, type) {
        if (confirm(`Are you sure you want to delete this ${type}?`)) {
            if (type === "holiday") {
                delete holidays[id];
            } else {
                delete events[id];
            }
            
            saveData();
            renderCalendar();
        }
    }

    function updateDateFields() {
        const type = eventTypeSelect.value;
        
        // Hide all date input groups
        singleDateGroup.style.display = "none";
        dateRangeGroup.style.display = "none";
        recurringDateGroup.style.display = "none";
        
        // Show appropriate date inputs based on type
        if (type === "single") {
            singleDateGroup.style.display = "block";
        } else if (type === "range") {
            dateRangeGroup.style.display = "block";
        } else if (type === "recurring") {
            recurringDateGroup.style.display = "block";
        }
    }

    function openModal() {
        eventModal.style.display = "flex";
    }

    function closeModal() {
        eventModal.style.display = "none";
        eventForm.reset();
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

    addEventBtn.addEventListener("click", () => {
        eventModalTitle.textContent = "Add Event";
        document.getElementById("edit-type").value = "event";
        document.getElementById("edit-id").value = "";
        document.getElementById("event-name").value = "";
        document.getElementById("event-type").value = "single";
        updateDateFields();
        openModal();
    });

    addHolidayBtn.addEventListener("click", () => {
        eventModalTitle.textContent = "Add Holiday";
        document.getElementById("edit-type").value = "holiday";
        document.getElementById("edit-id").value = "";
        document.getElementById("event-name").value = "";
        document.getElementById("event-type").value = "single";
        updateDateFields();
        openModal();
    });

    eventTypeSelect.addEventListener("change", updateDateFields);

    closeModalBtn.addEventListener("click", closeModal);

    // Close modal when clicking outside
    window.addEventListener("click", (e) => {
        if (e.target === eventModal) {
            closeModal();
        }
    });

    // Form submission
    eventForm.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const name = document.getElementById("event-name").value;
        const type = document.getElementById("event-type").value;
        const editType = document.getElementById("edit-type").value;
        const editId = document.getElementById("edit-id").value;
        
        let key = "";
        
        // Generate the appropriate key based on date type
        if (type === "recurring") {
            const month = document.getElementById("recurring-month").value;
            const day = String(document.getElementById("recurring-day").value).padStart(2, '0');
            key = `${month}-${day}`;
        } else if (type === "single") {
            key = document.getElementById("event-date").value;
        } else if (type === "range") {
            const startDate = document.getElementById("event-start-date").value;
            const endDate = document.getElementById("event-end-date").value;
            key = `${startDate}_to_${endDate}`;
        }
        
        // Create or update the entry
        const entry = {
            name: name,
            type: type
        };
        
        // If editing, remove the old entry
        if (editId && editId !== key) {
            if (editType === "holiday") {
                delete holidays[editId];
            } else {
                delete events[editId];
            }
        }
        
        // Add the new/updated entry
        if (editType === "holiday") {
            holidays[key] = entry;
        } else {
            events[key] = entry;
        }
        
        // Save and update
        saveData();
        renderCalendar();
        closeModal();
    });

    // Initialize
    updateDateFields();
    renderCalendar();
</script>
</body>
</html>