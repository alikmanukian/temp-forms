# DateFilter Between Clause Range Selection Fix

## Problem
When selecting "Between" clause in DateFilter, the component doesn't work properly for date range selection. Current issues:
1. Line 108 in DateFilter.vue has RangeCalendar but it's not properly bound to the model
2. Missing proper handling of date ranges (two values: start and end)
3. Backend FiltersBetween expects an array of two dates, but frontend sends single date
4. No proper synchronization between DateFilter and FilterDropdown

## Tasks

### 1. Fix data model for date ranges ✅
- [x] Modify `dateValue` in DateFilter.vue to support object with start/end dates for Between clause
- [x] Add separate logic for handling date ranges vs single dates

### 2. Update RangeCalendar integration ✅
- [x] Fix v-model binding for RangeCalendar on line 108 DateFilter.vue
- [x] Add proper event handling for range calendar
- [x] Implement range to string format conversion for API

### 3. Update event emission logic ✅
- [x] Modify `search()` function to properly send date ranges
- [x] Ensure Between clause sends array of two dates in "start,end" format
- [x] Add validation that both dates are selected before sending

### 4. Improve UX ✅
- [x] Add placeholder text for date ranges
- [x] Show selected range in FilterDropdown label
- [x] Add ability to clear the range

### 5. Testing ✅
- [x] Test Between clause with range selection
- [x] Ensure regular clauses (Equals, After, Before) work as before
- [x] Verify backend FiltersBetween receives proper data

## Technical Information

### Current problematic code:
- `resources/js/components/table/components/filters/inputs/DateFilter.vue:108` - incorrect RangeCalendar binding
- `resources/js/components/table/components/filters/inputs/DateFilter.vue:66-76` - onDateSelect function doesn't handle ranges
- `app/TableComponents/Filters/Spatie/FiltersBetween.php` - expects comma-separated date array

### Required backend format:
For Between clause need to send: `"2024-01-01,2024-01-31"` (start_date,end_date)