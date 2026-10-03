const birthDateDisplay = document.getElementById("birth-date-display");
if (birthDateDisplay) {
  const birthDateValue = document.getElementById("birth_date");
  const birthCalendar = document.getElementById("signup-jalali-calendar");
  const birthMonths = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"];
  const birthWeekdays = ["ش", "ی", "د", "س", "چ", "پ", "ج"];
  const persianNumber = new Intl.NumberFormat("fa-IR");
  let birthCalendarYear;
  let birthCalendarMonth;

  const toLatinDigits = value => String(value)
    .replace(/[۰-۹]/g, digit => "۰۱۲۳۴۵۶۷۸۹".indexOf(digit))
    .replace(/[٠-٩]/g, digit => "٠١٢٣٤٥٦٧٨٩".indexOf(digit));
  const toPersianDigits = value => String(value).replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);

  function birthJalaliToGregorian(year, month, day) {
    let jy = year + 1595;
    let days = -355668 + (365 * jy) + (Math.floor(jy / 33) * 8)
      + Math.floor(((jy % 33) + 3) / 4) + day + (month < 7 ? (month - 1) * 31 : ((month - 7) * 30) + 186);
    let gy = 400 * Math.floor(days / 146097);
    days %= 146097;
    if (days > 36524) {
      gy += 100 * Math.floor(--days / 36524);
      days %= 36524;
      if (days >= 365) days++;
    }
    gy += 4 * Math.floor(days / 1461);
    days %= 1461;
    if (days > 365) {
      gy += Math.floor((days - 1) / 365);
      days = (days - 1) % 365;
    }
    const monthDays = [31, (gy % 4 === 0 && (gy % 100 !== 0 || gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    let gm = 0;
    while (days >= monthDays[gm]) days -= monthDays[gm++];
    return `${gy}-${String(gm + 1).padStart(2, "0")}-${String(days + 1).padStart(2, "0")}`;
  }

  function birthFormatJalali(gregorianDate) {
    const parts = new Intl.DateTimeFormat("en-US-u-ca-persian-nu-latn", {
      year: "numeric", month: "2-digit", day: "2-digit", timeZone: "UTC"
    }).formatToParts(new Date(`${gregorianDate}T00:00:00Z`));
    const part = type => parts.find(item => item.type === type).value;
    return `${part("year")}/${part("month")}/${part("day")}`;
  }

  function birthToday() {
    const now = new Date();
    return birthFormatJalali(`${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`);
  }

  function birthMonthLength(year, month) {
    if (month <= 6) return 31;
    if (month <= 11) return 30;
    return birthFormatJalali(birthJalaliToGregorian(year, 12, 30)) === `${year}/12/30` ? 30 : 29;
  }

  function renderBirthCalendar() {
    const firstDay = new Date(`${birthJalaliToGregorian(birthCalendarYear, birthCalendarMonth, 1)}T00:00:00Z`).getUTCDay();
    const offset = (firstDay + 1) % 7;
    const blankDays = Array.from({ length: offset }, () => "<span></span>").join("");
    const days = Array.from({ length: birthMonthLength(birthCalendarYear, birthCalendarMonth) }, (_, index) => {
      const day = index + 1;
      const value = `${birthCalendarYear}/${String(birthCalendarMonth).padStart(2, "0")}/${String(day).padStart(2, "0")}`;
      const selected = birthDateValue.value === value ? " is-selected" : "";
      return `<button class="jalali-calendar-day${selected}" type="button" data-birth-day="${day}">${persianNumber.format(day)}</button>`;
    }).join("");
    birthCalendar.innerHTML = `
      <div class="jalali-calendar-header">
        <button class="jalali-calendar-nav" type="button" data-birth-nav="next" aria-label="ماه بعد">›</button>
        <div class="jalali-calendar-selects">
          <select class="jalali-calendar-select" data-birth-month aria-label="انتخاب ماه">${birthMonths.map((month, index) => `<option value="${index + 1}" ${birthCalendarMonth === index + 1 ? "selected" : ""}>${month}</option>`).join("")}</select>
          <select class="jalali-calendar-select" data-birth-year aria-label="انتخاب سال">${Array.from({ length: 126 }, (_, index) => 1300 + index).map(year => `<option value="${year}" ${birthCalendarYear === year ? "selected" : ""}>${persianNumber.format(year)}</option>`).join("")}</select>
        </div>
        <button class="jalali-calendar-nav" type="button" data-birth-nav="previous" aria-label="ماه قبل">‹</button>
      </div>
      <div class="jalali-calendar-weekdays">${birthWeekdays.map(day => `<span>${day}</span>`).join("")}</div>
      <div class="jalali-calendar-days">${blankDays}${days}</div>`;
  }

  function openBirthCalendar() {
    const value = toLatinDigits(birthDateValue.value || birthToday());
    [birthCalendarYear, birthCalendarMonth] = value.split("/").map(Number);
    renderBirthCalendar();
    birthCalendar.hidden = false;
    birthDateDisplay.setAttribute("aria-expanded", "true");
  }

  function closeBirthCalendar() {
    birthCalendar.hidden = true;
    birthDateDisplay.setAttribute("aria-expanded", "false");
  }

  birthCalendar.addEventListener("click", event => {
    const nav = event.target.closest("[data-birth-nav]");
    if (nav) {
      event.preventDefault();
      birthCalendarMonth += nav.dataset.birthNav === "next" ? 1 : -1;
      if (birthCalendarMonth === 13) { birthCalendarMonth = 1; birthCalendarYear++; }
      if (birthCalendarMonth === 0) { birthCalendarMonth = 12; birthCalendarYear--; }
      renderBirthCalendar();
      return;
    }
    const day = event.target.closest("[data-birth-day]");
    if (day) {
      birthDateValue.value = `${birthCalendarYear}/${String(birthCalendarMonth).padStart(2, "0")}/${String(day.dataset.birthDay).padStart(2, "0")}`;
      birthDateDisplay.value = toPersianDigits(birthDateValue.value);
      closeBirthCalendar();
    }
  });

  birthCalendar.addEventListener("change", event => {
    if (event.target.matches("[data-birth-month]")) birthCalendarMonth = Number(event.target.value);
    if (event.target.matches("[data-birth-year]")) birthCalendarYear = Number(event.target.value);
    renderBirthCalendar();
  });

  birthDateDisplay.value = toPersianDigits(birthDateValue.value);
  document.querySelector("[data-signup-date-picker]").addEventListener("click", openBirthCalendar);
  birthDateDisplay.addEventListener("click", openBirthCalendar);
  document.addEventListener("click", event => {
    if (!birthCalendar.hidden && !event.target.closest(".jalali-date-wrap") && !event.target.closest("#signup-jalali-calendar")) closeBirthCalendar();
  });
}
