<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title> Calendar</title>
  <link rel="stylesheet" href="styles.css">

  <style>
    .main-box{
      display:flex;
      width: 100%;
      column-gap:10%
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
    }
.content{
  flex:5;
}
aside{
  flex:1.35;
}
    h1 {
      margin: 20px 0;
      font-size: 28px;
      color: #fff;
    }

    .calendar-container {
      flex:1;
      width: 90%;
      max-width: 900px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #4caf50;
      color: white;
      padding: 15px 20px;
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
    }

    .calendar-header button:hover {
      background: rgba(255, 255, 255, 0.2);
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
      grid-auto-rows: 60px;
      gap: 5px;
    }

    .days span {
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f9f9f9;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .days span:hover {
      background: #4caf50;
      color: white;
    }

    .days .today {
      background-color: #87CEEB;
      /* Sky Blue */
      color: white;
      font-weight: bold;
    }

    .days .important-day {
      background-color: #FF6347;
      /* Red */
      color: white;
      font-weight: bold;
    }

    .important-days {
      margin: 20px 0;
      width: 90%;
      max-width: 900px;
    }

    .important-days ul {
      list-style: none;
      padding: 0;
    }

    .important-days ul li {
      background: #FF6347;
      /* Red */
      color: white;
      margin: 5px 0;
      padding: 10px;
      border-radius: 5px;
      font-size: 16px;
    }
  </style>
</head>

<body>
  <div class="main-box">
      
<?php
      include 'sidebar.php';
    ?>

<script>
  document.querySelector('a[href="calender.php"]').classList.add('active-page');
</script>
<div class="content">

  <h1> Calendar</h1>
  <div class="calendar-container">
    <div class="calendar-header">
      <button id="prev-month">❮</button>
      <h2 id="month-year"></h2>
      <button id="next-month">❯</button>
    </div>
    <div class="calendar-body">
      <div class="day-names">
        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
      </div>
      <div class="days" id="days"></div>
    </div>
  </div>
  <div class="important-days">
    <h2>Important Days & Festivals</h2>
    <ul id="important-days-list"></ul>
  </div>

</div>

  </div>

  <script>
    const monthYearEl = document.getElementById("month-year");
    const daysEl = document.getElementById("days");
    const importantDaysListEl = document.getElementById("important-days-list");
    const prevMonthBtn = document.getElementById("prev-month");
    const nextMonthBtn = document.getElementById("next-month");

    let currentDate = new Date();

    const importantDays = {
      "01-01": "English New Year",
      "02-14": "Valentine’s Day",
      "03-08": "International Women’s Day",
      "04-14": "Nepali New Year",
      "05-01": "International Workers’ Day",
      "06-15": "Father’s Day (Nepal)",
      "08-19": "Gai Jatra",
      "08-22": "Krishna Janmashtami",
      "09-28": "Indra Jatra",
      "10-14": "Dashain (Vijaya Dashami)",
      "11-12": "Tihar (Laxmi Puja)",
      "11-15": "Bhai Tika (Tihar)",
      "12-25": "Christmas",
    };

    function renderCalendar(date) {
      const year = date.getFullYear();
      const month = date.getMonth();

      const today = new Date();
      const firstDayOfMonth = new Date(year, month, 1).getDay();
      const lastDateOfMonth = new Date(year, month + 1, 0).getDate();

      monthYearEl.textContent = `${date.toLocaleString("default", { month: "long" })} ${year}`;

      daysEl.innerHTML = "";
      importantDaysListEl.innerHTML = "";

      for (let i = 0; i < firstDayOfMonth; i++) {
        const emptyDay = document.createElement("span");
        daysEl.appendChild(emptyDay);
      }

      for (let i = 1; i <= lastDateOfMonth; i++) {
        const dayEl = document.createElement("span");
        const fullDate = `${String(month + 1).padStart(2, "0")}-${String(i).padStart(2, "0")}`;

        dayEl.textContent = i;

        if (
          today.getDate() === i &&
          today.getMonth() === month &&
          today.getFullYear() === year
        ) {
          dayEl.classList.add("today");
        }

        if (importantDays[fullDate]) {
          dayEl.classList.add("important-day");

          const li = document.createElement("li");
          li.textContent = `${i} ${date.toLocaleString("default", { month: "long" })}: ${importantDays[fullDate]}`;
          importantDaysListEl.appendChild(li);
        }

        daysEl.appendChild(dayEl);
      }
    }

    prevMonthBtn.addEventListener("click", () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar(currentDate);
    });

    nextMonthBtn.addEventListener("click", () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar(currentDate);
    });

    renderCalendar(currentDate);
  </script>


</body>

</html>