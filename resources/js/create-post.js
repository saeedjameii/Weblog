const form = document.getElementById("listing-form");
if (form) {
const title = document.getElementById("post-title");
const description = document.getElementById("description");
const category = document.getElementById("category");
const condition = document.getElementById("condition");
const provinceInput = document.getElementById("province-id");
const cityInput = document.getElementById("city-id");
const firstDay = document.getElementById("first-day-price");
const extraDay = document.getElementById("extra-day-price");
const firstDayValue = document.getElementById("first-day-price-value");
const extraDayValue = document.getElementById("extra-day-price-value");
const startDate = document.getElementById("available-start");
const endDate = document.getElementById("available-end");
const startDateValue = document.getElementById("available-start-value");
const endDateValue = document.getElementById("available-end-value");
const jalaliCalendar = document.getElementById("jalali-calendar");
const photoInput = document.getElementById("post-photos");
const thumbnailStrip = document.getElementById("thumbnail-strip");
const photoStatus = document.getElementById("photo-status");
const characterCount = document.getElementById("character-count");
const formStatus = document.getElementById("form-status");
const draftButton = document.getElementById("save-draft-button");

const previewTitle = document.getElementById("preview-title");
const previewCategory = document.getElementById("preview-category");
const previewMeta = document.getElementById("preview-meta");
const previewPrice = document.getElementById("preview-price");
const previewImage = document.getElementById("preview-image");
const toolIllustration = document.querySelector(".post-illustration");

const tomanFormatter = new Intl.NumberFormat("fa-IR", { maximumFractionDigits: 0 });

function normalizeToman(value) {
  const latinDigits = String(value)
    .replace(/[۰-۹]/g, digit => "۰۱۲۳۴۵۶۷۸۹".indexOf(digit))
    .replace(/[٠-٩]/g, digit => "٠١٢٣٤٥٦٧٨٩".indexOf(digit))
    .replace(/[٬,\s]/g, "")
    .split(/[.٫]/, 1)[0]
    .replace(/[^\d]/g, "");

  return latinDigits === "" ? "" : String(Number(latinDigits));
}

function formatToman(value) {
  const amount = normalizeToman(value);
  return amount === "" ? "" : tomanFormatter.format(Number(amount));
}

function currency(value){
  const amount = normalizeToman(value);
  return amount === "" ? "۰ تومان" : `${tomanFormatter.format(Number(amount))} تومان`;
}

function syncPriceInput(input, valueInput) {
  const amount = normalizeToman(input.value);
  valueInput.value = amount;
  input.value = formatToman(amount);
}

function initializePriceInput(input, valueInput) {
  input.value = formatToman(valueInput.value);
  input.addEventListener("input", () => {
    syncPriceInput(input, valueInput);
    updatePreview();
  });
}

function normalizeDigits(value) {
  return String(value)
    .replace(/[۰-۹]/g, digit => "۰۱۲۳۴۵۶۷۸۹".indexOf(digit))
    .replace(/[٠-٩]/g, digit => "٠١٢٣٤٥٦٧٨٩".indexOf(digit));
}

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

  const gregorianMonthDays = [31, (gy % 4 === 0 && (gy % 100 !== 0 || gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
  let gm = 0;
  while (days >= gregorianMonthDays[gm]) days -= gregorianMonthDays[gm++];

  return `${gy}-${String(gm + 1).padStart(2, "0")}-${String(days + 1).padStart(2, "0")}`;
}

function formatJalaliDate(gregorianDate) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(gregorianDate)) return "";

  const date = new Date(`${gregorianDate}T00:00:00Z`);
  const parts = new Intl.DateTimeFormat("en-US-u-ca-persian-nu-latn", {
    year: "numeric", month: "2-digit", day: "2-digit", timeZone: "UTC"
  }).formatToParts(date);
  const value = type => parts.find(part => part.type === type).value;

  return `${value("year")}/${value("month")}/${value("day")}`;
}

function syncJalaliDateInput(input, valueInput) {
  const match = normalizeDigits(input.value).match(/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})$/);
  if (!match) {
    valueInput.value = "";
    input.setCustomValidity(input.value ? "تاریخ را به صورت ۱۴۰۵/۰۶/۲۴ وارد کنید." : "");
    return;
  }

  const jalaliDate = `${match[1]}/${match[2].padStart(2, "0")}/${match[3].padStart(2, "0")}`;
  const gregorianDate = jalaliToGregorian(Number(match[1]), Number(match[2]), Number(match[3]));
  if (formatJalaliDate(gregorianDate) !== jalaliDate) {
    valueInput.value = "";
    input.setCustomValidity("تاریخ شمسی معتبر نیست.");
    return;
  }

  valueInput.value = gregorianDate;
  input.value = jalaliDate.replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);
  input.setCustomValidity("");
}

function initializeJalaliDateInput(input, valueInput) {
  input.value = formatJalaliDate(valueInput.value).replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);
  input.addEventListener("input", () => syncJalaliDateInput(input, valueInput));
  input.addEventListener("blur", () => syncJalaliDateInput(input, valueInput));
}

const jalaliMonthNames = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"];
const jalaliWeekdays = ["ش", "ی", "د", "س", "چ", "پ", "ج"];
let activeDateInput;
let activeDateValueInput;
let calendarYear;
let calendarMonth;

function getJalaliParts(gregorianDate) {
  return formatJalaliDate(gregorianDate).split("/").map(Number);
}

function localGregorianDate() {
  const now = new Date();
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
}

function jalaliMonthLength(year, month) {
  if (month <= 6) return 31;
  if (month <= 11) return 30;
  return formatJalaliDate(jalaliToGregorian(year, 12, 30)) === `${year}/12/30` ? 30 : 29;
}

function renderJalaliCalendar() {
  const firstDay = new Date(`${jalaliToGregorian(calendarYear, calendarMonth, 1)}T00:00:00Z`).getUTCDay();
  const firstDayOffset = (firstDay + 1) % 7;
  const selectedDate = activeDateValueInput.value;
  const today = localGregorianDate();
  const emptyDays = Array.from({ length: firstDayOffset }, () => '<span></span>').join("");
  const days = Array.from({ length: jalaliMonthLength(calendarYear, calendarMonth) }, (_, index) => {
    const day = index + 1;
    const gregorianDate = jalaliToGregorian(calendarYear, calendarMonth, day);
    const classes = ["jalali-calendar-day"];
    if (gregorianDate === selectedDate) classes.push("is-selected");
    if (gregorianDate === today) classes.push("is-today");
    return `<button class="${classes.join(" ")}" type="button" data-jalali-day="${day}">${new Intl.NumberFormat("fa-IR").format(day)}</button>`;
  }).join("");

  jalaliCalendar.innerHTML = `
    <div class="jalali-calendar-header">
      <button class="jalali-calendar-nav" type="button" data-calendar-nav="next" aria-label="ماه بعد">›</button>
      <div class="jalali-calendar-selects">
        <select class="jalali-calendar-select" data-calendar-month aria-label="انتخاب ماه">
          ${jalaliMonthNames.map((name, index) => `<option value="${index + 1}" ${calendarMonth === index + 1 ? "selected" : ""}>${name}</option>`).join("")}
        </select>
        <select class="jalali-calendar-select" data-calendar-year aria-label="انتخاب سال">
          ${Array.from({ length: 151 }, (_, index) => 1300 + index).map(year => `<option value="${year}" ${calendarYear === year ? "selected" : ""}>${new Intl.NumberFormat("fa-IR").format(year)}</option>`).join("")}
        </select>
      </div>
      <button class="jalali-calendar-nav" type="button" data-calendar-nav="previous" aria-label="ماه قبل">‹</button>
    </div>
    <div class="jalali-calendar-weekdays">${jalaliWeekdays.map(day => `<span>${day}</span>`).join("")}</div>
    <div class="jalali-calendar-days">${emptyDays}${days}</div>`;
}

function closeJalaliCalendar() {
  jalaliCalendar.hidden = true;
  if (activeDateInput) activeDateInput.setAttribute("aria-expanded", "false");
}

function openJalaliCalendar(input, valueInput) {
  if (!jalaliCalendar.hidden && activeDateInput === input) {
    closeJalaliCalendar();
    return;
  }

  activeDateInput = input;
  activeDateValueInput = valueInput;
  const [year, month] = getJalaliParts(valueInput.value || localGregorianDate());
  calendarYear = year;
  calendarMonth = month;
  renderJalaliCalendar();
  jalaliCalendar.hidden = false;
  input.setAttribute("aria-expanded", "true");
}

jalaliCalendar.addEventListener("click", event => {
  const navigation = event.target.closest("[data-calendar-nav]");
  if (navigation) {
    event.preventDefault();
    calendarMonth += navigation.dataset.calendarNav === "next" ? 1 : -1;
    if (calendarMonth === 13) { calendarMonth = 1; calendarYear++; }
    if (calendarMonth === 0) { calendarMonth = 12; calendarYear--; }
    renderJalaliCalendar();
    return;
  }

  const day = event.target.closest("[data-jalali-day]");
  if (day) {
    activeDateInput.value = `${calendarYear}/${String(calendarMonth).padStart(2, "0")}/${String(day.dataset.jalaliDay).padStart(2, "0")}`;
    syncJalaliDateInput(activeDateInput, activeDateValueInput);
    closeJalaliCalendar();
  }
});

jalaliCalendar.addEventListener("change", event => {
  if (event.target.matches("[data-calendar-month]")) calendarMonth = Number(event.target.value);
  if (event.target.matches("[data-calendar-year]")) calendarYear = Number(event.target.value);
  renderJalaliCalendar();
});

document.querySelectorAll("[data-date-picker-for]").forEach(button => {
  const input = document.getElementById(button.dataset.datePickerFor);
  const valueInput = document.getElementById(`${button.dataset.datePickerFor}-value`);
  button.addEventListener("click", () => openJalaliCalendar(input, valueInput));
  input.addEventListener("click", () => openJalaliCalendar(input, valueInput));
});

document.addEventListener("click", event => {
  if (!jalaliCalendar.hidden && !event.target.closest(".jalali-date-wrap") && !event.target.closest("#jalali-calendar")) {
    closeJalaliCalendar();
  }
});

function updatePreview(){
  previewTitle.textContent = title.value.trim() || "نام ابزار";
  previewCategory.textContent = category.value || "دسته‌بندی";
  previewMeta.textContent = (condition.value || "شرایط") + " · " + (cityInput.selectedOptions[0]?.text || "شهر");
  previewPrice.textContent = currency(firstDay.value) + " روز اول · " + currency(extraDay.value) + " روز اضافی";
}

function updateCharacterCount(){
  characterCount.textContent = description.value.length + " / 1000";
}

function showPhotoPreviews(files){
  thumbnailStrip.replaceChildren();
  const selected = Array.from(files).slice(0,5);

  if (selected.length) {
    const reader = new FileReader();
    reader.onload = event => {
      previewImage.src = event.target.result;
      previewImage.hidden = false;
      toolIllustration.hidden = true;
    };
    reader.readAsDataURL(selected[0]);
  } else {
    previewImage.removeAttribute("src");
    previewImage.hidden = true;
    toolIllustration.hidden = false;
  }

  selected.forEach(file=>{
    const thumb=document.createElement("div");
    thumb.className="local-thumb";
    const reader=new FileReader();
    reader.onload=e=>thumb.style.backgroundImage=`url("${e.target.result}")`;
    reader.readAsDataURL(file);
    thumbnailStrip.appendChild(thumb);
  });

  if(files.length>5) photoStatus.textContent="Showing the first 5 selected photos.";
  else if(selected.length) photoStatus.textContent=selected.length+" photo"+(selected.length===1?"":"s")+" selected for this preview.";
  else photoStatus.textContent="";
}

[title,category,condition,provinceInput,cityInput].forEach(input=>{
  input.addEventListener("input",updatePreview);
  input.addEventListener("change",updatePreview);
});

description.addEventListener("input",updateCharacterCount);
photoInput.addEventListener("change",()=>showPhotoPreviews(photoInput.files));

updateCharacterCount();
initializePriceInput(firstDay, firstDayValue);
initializePriceInput(extraDay, extraDayValue);
initializeJalaliDateInput(startDate, startDateValue);
initializeJalaliDateInput(endDate, endDateValue);
updatePreview();
}

document.querySelectorAll("[data-province-select]").forEach(provinceSelect => {
  const citySelect = document.getElementById(provinceSelect.dataset.citySelect);
  const selectedCityId = citySelect.dataset.selectedCity;

  function setCityPlaceholder(text) {
    citySelect.replaceChildren(new Option(text, ""));
  }

  async function loadCities(provinceId, cityId = "") {
    if (!provinceId) {
      setCityPlaceholder("ابتدا استان را انتخاب کنید");
      citySelect.disabled = true;
      return;
    }

    setCityPlaceholder("در حال دریافت شهرها...");
    citySelect.disabled = true;

    try {
      const response = await fetch(provinceSelect.dataset.citiesUrl.replace("__province__", encodeURIComponent(provinceId)), {
        headers: { Accept: "application/json" },
      });
      if (!response.ok) throw new Error("Unable to load cities");

      const cities = await response.json();
      setCityPlaceholder("شهر را انتخاب کنید");
      cities.forEach(city => citySelect.add(new Option(city.name, city.id, false, String(city.id) === String(cityId))));
      citySelect.disabled = false;
      citySelect.dispatchEvent(new Event("change"));
    } catch (error) {
      setCityPlaceholder("دریافت شهرها ناموفق بود");
      citySelect.disabled = true;
    }
  }

  provinceSelect.addEventListener("change", () => loadCities(provinceSelect.value));
  if (provinceSelect.value) loadCities(provinceSelect.value, selectedCityId);
});

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

const editFirstDayPrice = document.getElementById("edit-first-day-price");
if (editFirstDayPrice) {
  const editPriceFormatter = new Intl.NumberFormat("fa-IR", { maximumFractionDigits: 0 });

  function normalizeEditToman(value) {
    const latinDigits = String(value)
      .replace(/[۰-۹]/g, digit => "۰۱۲۳۴۵۶۷۸۹".indexOf(digit))
      .replace(/[٠-٩]/g, digit => "٠١٢٣٤٥٦٧٨٩".indexOf(digit))
      .replace(/[٬,\s]/g, "")
      .split(/[.٫]/, 1)[0]
      .replace(/[^\d]/g, "");

    return latinDigits === "" ? "" : String(Number(latinDigits));
  }

  function initializeEditTomanInput(inputId) {
    const input = document.getElementById(inputId);
    const valueInput = document.getElementById(`${inputId}-value`);
    const sync = () => {
      const amount = normalizeEditToman(input.value);
      valueInput.value = amount;
      input.value = amount === "" ? "" : editPriceFormatter.format(Number(amount));
    };

    input.value = valueInput.value === "" ? "" : editPriceFormatter.format(Number(normalizeEditToman(valueInput.value)));
    input.addEventListener("input", sync);
  }

  initializeEditTomanInput("edit-first-day-price");
  initializeEditTomanInput("edit-extra-day-price");
}

const editStartDate = document.getElementById("edit-available-start");
if (editStartDate) {
  const editEndDate = document.getElementById("edit-available-end");
  const editStartDateValue = document.getElementById("edit-available-start-value");
  const editEndDateValue = document.getElementById("edit-available-end-value");
  const editCalendar = document.getElementById("edit-jalali-calendar");
  const editMonths = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند"];
  const editWeekdays = ["ش", "ی", "د", "س", "چ", "پ", "ج"];
  const editNumberFormatter = new Intl.NumberFormat("fa-IR");
  let activeEditDateInput;
  let activeEditDateValueInput;
  let editCalendarYear;
  let editCalendarMonth;

  function editJalaliToGregorian(year, month, day) {
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

  function formatEditJalaliDate(gregorianDate) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(gregorianDate)) return "";
    const parts = new Intl.DateTimeFormat("en-US-u-ca-persian-nu-latn", {
      year: "numeric", month: "2-digit", day: "2-digit", timeZone: "UTC"
    }).formatToParts(new Date(`${gregorianDate}T00:00:00Z`));
    const part = type => parts.find(item => item.type === type).value;
    return `${part("year")}/${part("month")}/${part("day")}`;
  }

  function editLocalGregorianDate() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
  }

  function editMonthLength(year, month) {
    if (month <= 6) return 31;
    if (month <= 11) return 30;
    return formatEditJalaliDate(editJalaliToGregorian(year, 12, 30)) === `${year}/12/30` ? 30 : 29;
  }

  function renderEditCalendar() {
    const firstDay = new Date(`${editJalaliToGregorian(editCalendarYear, editCalendarMonth, 1)}T00:00:00Z`).getUTCDay();
    const offset = (firstDay + 1) % 7;
    const blanks = Array.from({ length: offset }, () => "<span></span>").join("");
    const days = Array.from({ length: editMonthLength(editCalendarYear, editCalendarMonth) }, (_, index) => {
      const day = index + 1;
      const gregorian = editJalaliToGregorian(editCalendarYear, editCalendarMonth, day);
      const selected = gregorian === activeEditDateValueInput.value ? " is-selected" : "";
      return `<button class="jalali-calendar-day${selected}" type="button" data-edit-jalali-day="${day}">${editNumberFormatter.format(day)}</button>`;
    }).join("");
    editCalendar.innerHTML = `
      <div class="jalali-calendar-header">
        <button class="jalali-calendar-nav" type="button" data-edit-calendar-nav="next" aria-label="ماه بعد">›</button>
        <div class="jalali-calendar-selects">
          <select class="jalali-calendar-select" data-edit-calendar-month aria-label="انتخاب ماه">${editMonths.map((month, index) => `<option value="${index + 1}" ${editCalendarMonth === index + 1 ? "selected" : ""}>${month}</option>`).join("")}</select>
          <select class="jalali-calendar-select" data-edit-calendar-year aria-label="انتخاب سال">${Array.from({ length: 151 }, (_, index) => 1300 + index).map(year => `<option value="${year}" ${editCalendarYear === year ? "selected" : ""}>${editNumberFormatter.format(year)}</option>`).join("")}</select>
        </div>
        <button class="jalali-calendar-nav" type="button" data-edit-calendar-nav="previous" aria-label="ماه قبل">‹</button>
      </div>
      <div class="jalali-calendar-weekdays">${editWeekdays.map(day => `<span>${day}</span>`).join("")}</div>
      <div class="jalali-calendar-days">${blanks}${days}</div>`;
  }

  function closeEditCalendar() {
    editCalendar.hidden = true;
    if (activeEditDateInput) activeEditDateInput.setAttribute("aria-expanded", "false");
  }

  function openEditCalendar(input, valueInput) {
    if (!editCalendar.hidden && activeEditDateInput === input) {
      closeEditCalendar();
      return;
    }
    activeEditDateInput = input;
    activeEditDateValueInput = valueInput;
    [editCalendarYear, editCalendarMonth] = formatEditJalaliDate(valueInput.value || editLocalGregorianDate()).split("/").map(Number);
    renderEditCalendar();
    editCalendar.hidden = false;
    input.setAttribute("aria-expanded", "true");
  }

  editCalendar.addEventListener("click", event => {
    const nav = event.target.closest("[data-edit-calendar-nav]");
    if (nav) {
      event.preventDefault();
      editCalendarMonth += nav.dataset.editCalendarNav === "next" ? 1 : -1;
      if (editCalendarMonth === 13) { editCalendarMonth = 1; editCalendarYear++; }
      if (editCalendarMonth === 0) { editCalendarMonth = 12; editCalendarYear--; }
      renderEditCalendar();
      return;
    }
    const day = event.target.closest("[data-edit-jalali-day]");
    if (day) {
      const selected = `${editCalendarYear}/${String(editCalendarMonth).padStart(2, "0")}/${String(day.dataset.editJalaliDay).padStart(2, "0")}`;
      activeEditDateValueInput.value = editJalaliToGregorian(editCalendarYear, editCalendarMonth, Number(day.dataset.editJalaliDay));
      activeEditDateInput.value = selected.replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);
      closeEditCalendar();
    }
  });

  editCalendar.addEventListener("change", event => {
    if (event.target.matches("[data-edit-calendar-month]")) editCalendarMonth = Number(event.target.value);
    if (event.target.matches("[data-edit-calendar-year]")) editCalendarYear = Number(event.target.value);
    renderEditCalendar();
  });

  [editStartDate, editEndDate].forEach((input, index) => {
    const valueInput = index === 0 ? editStartDateValue : editEndDateValue;
    input.value = formatEditJalaliDate(valueInput.value).replace(/\d/g, digit => "۰۱۲۳۴۵۶۷۸۹"[digit]);
    input.addEventListener("click", () => openEditCalendar(input, valueInput));
  });

  document.querySelectorAll("[data-edit-date-picker-for]").forEach(button => {
    button.addEventListener("click", () => {
      const input = document.getElementById(button.dataset.editDatePickerFor);
      openEditCalendar(input, document.getElementById(`${button.dataset.editDatePickerFor}-value`));
    });
  });

  document.addEventListener("click", event => {
    if (!editCalendar.hidden && !event.target.closest(".jalali-date-wrap") && !event.target.closest("#edit-jalali-calendar")) closeEditCalendar();
  });
}
