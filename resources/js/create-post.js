const persianNumber = new Intl.NumberFormat("fa-IR");
const birthMonths = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"];
const birthWeekdays = ["ش", "ی", "د", "س", "چ", "پ", "ج"];

const toLatinDigits = value => String(value)
  .replace(/[۰-۹]/g, digit => "۰۱۲۳۴۵۶۷۸۹".indexOf(digit))
  .replace(/[٠-٩]/g, digit => "٠١٢٣٤٥٦٧٨٩".indexOf(digit));
const toPersianDigits = value => String(value).replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);

function jalaliToGregorian(year, month, day) {
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

function formatJalali(gregorianDate) {
  const parts = new Intl.DateTimeFormat("en-US-u-ca-persian-nu-latn", {
    year: "numeric", month: "2-digit", day: "2-digit", timeZone: "UTC"
  }).formatToParts(new Date(`${gregorianDate}T00:00:00Z`));
  const part = type => parts.find(item => item.type === type).value;
  return `${part("year")}/${part("month")}/${part("day")}`;
}

function todayJalali() {
  const now = new Date();
  return formatJalali(`${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`);
}

function monthLength(year, month) {
  if (month <= 6) return 31;
  if (month <= 11) return 30;
  return formatJalali(jalaliToGregorian(year, 12, 30)) === `${year}/12/30` ? 30 : 29;
}

function initJalaliPicker({ displayId, inputId, calendarId, buttonSelector, clearSelector, minYear, maxYear }) {
  const display = document.getElementById(displayId);
  if (!display) return;
  const valueInput = document.getElementById(inputId);
  const calendar = document.getElementById(calendarId);
  if (!valueInput || !calendar) return;

  const pickerButton = document.querySelector(buttonSelector);
  const clearButton = clearSelector ? document.querySelector(clearSelector) : null;

  let currentYear;
  let currentMonth;

  function renderCalendar() {
    const firstDay = new Date(`${jalaliToGregorian(currentYear, currentMonth, 1)}T00:00:00Z`).getUTCDay();
    const offset = (firstDay + 1) % 7;
    const blankDays = Array.from({ length: offset }, () => "<span></span>").join("");
    const days = Array.from({ length: monthLength(currentYear, currentMonth) }, (_, index) => {
      const day = index + 1;
      const value = `${currentYear}/${String(currentMonth).padStart(2, "0")}/${String(day).padStart(2, "0")}`;
      const selected = valueInput.value === value ? " is-selected" : "";
      return `<button class="jalali-calendar-day${selected}" type="button" data-picker-day="${day}">${persianNumber.format(day)}</button>`;
    }).join("");

    const yearOptions = Array.from({ length: maxYear - minYear + 1 }, (_, index) => minYear + index)
      .map(year => `<option value="${year}" ${currentYear === year ? "selected" : ""}>${persianNumber.format(year)}</option>`)
      .join("");

    calendar.innerHTML = `
      <div class="jalali-calendar-header">
        <button class="jalali-calendar-nav" type="button" data-picker-nav="next" aria-label="ماه بعد">›</button>
        <div class="jalali-calendar-selects">
          <select class="jalali-calendar-select" data-picker-month aria-label="انتخاب ماه">${birthMonths.map((month, index) => `<option value="${index + 1}" ${currentMonth === index + 1 ? "selected" : ""}>${month}</option>`).join("")}</select>
          <select class="jalali-calendar-select" data-picker-year aria-label="انتخاب سال">${yearOptions}</select>
        </div>
        <button class="jalali-calendar-nav" type="button" data-picker-nav="previous" aria-label="ماه قبل">‹</button>
      </div>
      <div class="jalali-calendar-weekdays">${birthWeekdays.map(day => `<span>${day}</span>`).join("")}</div>
      <div class="jalali-calendar-days">${blankDays}${days}</div>`;
  }

  function openCalendar() {
    const val = toLatinDigits(valueInput.value || todayJalali());
    [currentYear, currentMonth] = val.split("/").map(Number);
    if (!currentYear || currentYear < minYear || currentYear > maxYear) {
      currentYear = Number(todayJalali().split("/")[0]);
      currentMonth = Number(todayJalali().split("/")[1]);
    }
    renderCalendar();
    calendar.hidden = false;
    display.setAttribute("aria-expanded", "true");
  }

  function closeCalendar() {
    calendar.hidden = true;
    display.setAttribute("aria-expanded", "false");
  }

  calendar.addEventListener("click", event => {
    const nav = event.target.closest("[data-picker-nav]");
    if (nav) {
      event.preventDefault();
      currentMonth += nav.dataset.pickerNav === "next" ? 1 : -1;
      if (currentMonth === 13) { currentMonth = 1; currentYear++; }
      if (currentMonth === 0) { currentMonth = 12; currentYear--; }
      renderCalendar();
      return;
    }
    const day = event.target.closest("[data-picker-day]");
    if (day) {
      valueInput.value = `${currentYear}/${String(currentMonth).padStart(2, "0")}/${String(day.dataset.pickerDay).padStart(2, "0")}`;
      display.value = toPersianDigits(valueInput.value);
      closeCalendar();
    }
  });

  calendar.addEventListener("change", event => {
    if (event.target.matches("[data-picker-month]")) currentMonth = Number(event.target.value);
    if (event.target.matches("[data-picker-year]")) currentYear = Number(event.target.value);
    renderCalendar();
  });

  if (valueInput.value) {
    display.value = toPersianDigits(valueInput.value);
  }

  if (pickerButton) {
    pickerButton.addEventListener("click", openCalendar);
  }
  display.addEventListener("click", openCalendar);

  if (clearButton) {
    clearButton.addEventListener("click", () => {
      valueInput.value = "";
      display.value = "";
      const timeInput = document.getElementById("published_time");
      if (timeInput) timeInput.value = "";
      closeCalendar();
    });
  }

  document.addEventListener("click", event => {
    if (!calendar.hidden && !event.target.closest(".jalali-date-wrap") && !event.target.closest("#" + calendarId)) {
      closeCalendar();
    }
  });
}

// 1. Birth date picker (User panel & register)
initJalaliPicker({
  displayId: "birth-date-display",
  inputId: "birth_date",
  calendarId: "signup-jalali-calendar",
  buttonSelector: "[data-signup-date-picker]",
  minYear: 1300,
  maxYear: 1425,
});

// 2. Post publish date picker (Panel post create & edit)
initJalaliPicker({
  displayId: "publish-date-display",
  inputId: "published_date",
  calendarId: "post-jalali-calendar",
  buttonSelector: "[data-publish-date-picker]",
  clearSelector: "[data-clear-publish-date]",
  minYear: 1400,
  maxYear: 1435,
});

const typeSelect = document.getElementById('category-type');
const parentSelect = document.getElementById('parent-select');

function filterParentOptions() {
    const selectedType = typeSelect.value;
    [...parentSelect.options].forEach(option => {
        if (!option.dataset.type) return; // گزینه‌ی "بدون والد"
        option.hidden = option.dataset.type !== selectedType;
    });
    if (parentSelect.selectedOptions[0]?.hidden) {
        parentSelect.value = '';
    }
}

typeSelect.addEventListener('change', filterParentOptions);
filterParentOptions(); // اجرای اولیه موقع لود صفحه
