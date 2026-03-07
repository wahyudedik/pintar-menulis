# 🧪 Testing Guide - Smart Variation System

**Date:** March 7, 2026  
**Version:** 2.0.0  
**Estimated Time:** 1-2 hours

---

## 🎯 Testing Objectives

1. Verify Simple Mode works correctly (first-time vs returning)
2. Verify Advanced Mode works correctly (free vs premium)
3. Verify pricing displays correctly
4. Verify radio button selection works
5. Verify backend logic handles all cases
6. Verify error handling works

---

## 📋 Pre-Testing Checklist

Before starting tests, ensure:
- [ ] Server is running (`php artisan serve`)
- [ ] Database is accessible
- [ ] Gemini API key is configured in `.env`
- [ ] You have at least 2 test accounts:
  - Account A: New user (never generated before)
  - Account B: Existing user (has generated before)

---

## 🧪 Test Cases

### TEST 1: Simple Mode - First Time User

**Objective:** Verify first-time users get 5 captions

**Steps:**
1. Login with Account A (new user, never generated)
2. Select "Mode Simpel (Untuk Pemula)"
3. Fill the 6 questions:
   - Jenis usaha: "Fashion & Pakaian"
   - Produk: "Baju anak umur 2 tahun"
   - Harga: "50.000"
   - Target: "Ibu-ibu Muda"
   - Tujuan: "Biar Langsung Beli (Closing)"
   - Platform: "Instagram"
4. Click "Bikin Caption Sekarang!"
5. Wait for result

**Expected Result:**
- ✅ Should receive 5 different captions
- ✅ Each caption should be numbered (1., 2., 3., 4., 5.)
- ✅ Green message shows: "🎉 Generate pertama: 5 variasi GRATIS!"
- ✅ Result appears in right panel
- ✅ Copy and Save buttons work

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 2: Simple Mode - Returning User

**Objective:** Verify returning users get 1 caption

**Steps:**
1. Stay logged in as Account A (now has history)
2. Stay in "Mode Simpel"
3. Fill the form again with different product:
   - Produk: "Celana anak umur 3 tahun"
   - (keep other fields same or change as desired)
4. Click "Bikin Caption Sekarang!"
5. Wait for result

**Expected Result:**
- ✅ Should receive 1 caption only (no numbering)
- ✅ Caption should be high quality (best one)
- ✅ Green message shows: "Generate berikutnya: 1 caption terbaik"
- ✅ Result appears in right panel
- ✅ Copy and Save buttons work

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 3: Advanced Mode - Default (Free)

**Objective:** Verify default advanced mode gives 1 caption

**Steps:**
1. Login with Account B (existing user)
2. Select "Mode Lengkap (Advanced)"
3. Fill the advanced form:
   - Category: "Quick Templates"
   - Jenis Konten: "Caption Instagram"
   - Brief: "Jual baju anak lucu dan nyaman untuk usia 2-5 tahun"
   - Tone: "Casual"
   - Platform: "Instagram"
4. **DO NOT** check the "Generate Multiple Captions" checkbox
5. Click "Generate dengan AI"
6. Wait for result

**Expected Result:**
- ✅ Should receive 1 caption only
- ✅ Caption should be high quality
- ✅ No numbering
- ✅ Result appears in right panel

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 4: Advanced Mode - Premium (5 Captions)

**Objective:** Verify premium option with 5 captions works

**Steps:**
1. Stay in "Mode Lengkap (Advanced)"
2. Fill the form (same as TEST 3 or different)
3. **CHECK** the "Generate Multiple Captions (Premium)" checkbox
4. **SELECT** radio button: "5 Captions - Rp 5,000"
5. Verify the radio button is selected (should have blue dot)
6. Click "Generate dengan AI"
7. Wait for result

**Expected Result:**
- ✅ Should receive 5 different captions
- ✅ Each caption numbered (1., 2., 3., 4., 5.)
- ✅ Result appears in right panel
- ✅ All captions are different from each other

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 5: Advanced Mode - Premium (10 Captions)

**Objective:** Verify premium option with 10 captions works

**Steps:**
1. Stay in "Mode Lengkap (Advanced)"
2. Fill the form
3. **CHECK** the premium checkbox
4. **SELECT** radio button: "10 Captions - Rp 9,000"
5. Click "Generate dengan AI"
6. Wait for result

**Expected Result:**
- ✅ Should receive 10 different captions
- ✅ Each caption numbered (1. through 10.)
- ✅ Result appears in right panel
- ✅ All captions are different

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 6: Advanced Mode - Premium (15 Captions)

**Objective:** Verify premium option with 15 captions works

**Steps:**
1. Stay in "Mode Lengkap (Advanced)"
2. Fill the form
3. **CHECK** the premium checkbox
4. **SELECT** radio button: "15 Captions - Rp 12,000"
5. Click "Generate dengan AI"
6. Wait for result

**Expected Result:**
- ✅ Should receive 15 different captions
- ✅ Each caption numbered (1. through 15.)
- ✅ Result appears in right panel
- ✅ All captions are different

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 7: Advanced Mode - Premium (20 Captions)

**Objective:** Verify premium option with 20 captions works

**Steps:**
1. Stay in "Mode Lengkap (Advanced)"
2. Fill the form
3. **CHECK** the premium checkbox
4. **SELECT** radio button: "20 Captions - Rp 15,000"
5. Click "Generate dengan AI"
6. Wait for result

**Expected Result:**
- ✅ Should receive 20 different captions
- ✅ Each caption numbered (1. through 20.)
- ✅ Result appears in right panel
- ✅ All captions are different

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 8: Mode Switching

**Objective:** Verify switching between modes works correctly

**Steps:**
1. Start in "Mode Simpel"
2. Fill some fields
3. Switch to "Mode Lengkap (Advanced)"
4. Verify form changes
5. Switch back to "Mode Simpel"
6. Verify form changes back

**Expected Result:**
- ✅ Mode toggle buttons work
- ✅ Form changes correctly
- ✅ No JavaScript errors in console
- ✅ UI is responsive

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 9: Checkbox Without Radio Selection

**Objective:** Verify behavior when checkbox is checked but no radio selected

**Steps:**
1. Go to "Mode Lengkap (Advanced)"
2. Fill the form
3. **CHECK** the premium checkbox
4. **DO NOT** select any radio button
5. Click "Generate dengan AI"
6. Observe behavior

**Expected Result:**
- ✅ Should use default value (5 captions)
- OR
- ✅ Should show validation error asking to select

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 10: Uncheck After Selection

**Objective:** Verify behavior when checkbox is unchecked after radio selection

**Steps:**
1. Go to "Mode Lengkap (Advanced)"
2. Fill the form
3. **CHECK** the premium checkbox
4. **SELECT** radio button: "10 Captions"
5. **UNCHECK** the premium checkbox
6. Click "Generate dengan AI"
7. Wait for result

**Expected Result:**
- ✅ Should generate 1 caption (free tier)
- ✅ Radio selection should be ignored
- ✅ No errors

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 11: Error Handling - Invalid API Key

**Objective:** Verify error handling when API key is invalid

**Steps:**
1. Temporarily change `GEMINI_API_KEY` in `.env` to invalid value
2. Run `php artisan config:clear`
3. Try to generate in any mode
4. Observe error message

**Expected Result:**
- ✅ Should show user-friendly error message
- ✅ Error mentions API key issue
- ✅ Error suggests solution (get new key from AI Studio)
- ✅ No server crash

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

**IMPORTANT:** Restore correct API key after this test!

---

### TEST 12: Loading States

**Objective:** Verify loading states work correctly

**Steps:**
1. Fill any form (Simple or Advanced)
2. Click generate button
3. Observe UI during generation

**Expected Result:**
- ✅ Button shows "Sedang Bikin..." or "Generating..."
- ✅ Button is disabled during generation
- ✅ Spinner/loading icon appears
- ✅ User cannot submit again while loading
- ✅ Loading state clears after result

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 13: Copy to Clipboard

**Objective:** Verify copy functionality works

**Steps:**
1. Generate any caption
2. Click "Copy to Clipboard" button
3. Paste in a text editor (Ctrl+V)

**Expected Result:**
- ✅ Button shows "Copied!" briefly
- ✅ Caption is copied to clipboard
- ✅ Pasted text matches generated caption
- ✅ Button returns to "Copy to Clipboard"

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 14: Save for Analytics

**Objective:** Verify save functionality works

**Steps:**
1. Generate any caption
2. Click "💾 Save for Analytics" button
3. Check database `caption_analytics` table

**Expected Result:**
- ✅ Button shows "✓ Saved!" after click
- ✅ Button becomes disabled
- ✅ Record appears in database
- ✅ Success message appears

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 15: Caption History Recording

**Objective:** Verify captions are recorded in history

**Steps:**
1. Generate captions in any mode
2. Check database `caption_histories` table
3. Go to "Caption History" menu
4. Verify captions appear

**Expected Result:**
- ✅ Each generated caption is recorded
- ✅ User ID is correct
- ✅ Category, platform, tone are saved
- ✅ Timestamp is correct
- ✅ Captions appear in Caption History page

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 16: Anti-Repetition Logic

**Objective:** Verify AI avoids repeating previous captions

**Steps:**
1. Generate caption with specific brief
2. Note the result
3. Generate again with SAME brief
4. Compare results

**Expected Result:**
- ✅ Second generation should be different
- ✅ Different hook/opening
- ✅ Different structure
- ✅ Different emoji pattern
- ✅ Different CTA

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 17: Temperature Adjustment

**Objective:** Verify AI temperature increases with usage

**Steps:**
1. Use Account A (new user)
2. Generate 5 captions (should use temp 0.7)
3. Generate 10 more captions (should use temp 0.8)
4. Generate 10 more captions (should use temp 0.9)
5. Check server logs for temperature values

**Expected Result:**
- ✅ First 10 generates: temp 0.7
- ✅ Next 10 generates: temp 0.8
- ✅ After 20 generates: temp 0.9
- ✅ Captions become more creative over time

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 18: Browser Compatibility

**Objective:** Verify system works across browsers

**Browsers to Test:**
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

**Steps:**
1. Open application in each browser
2. Test basic flow (Simple Mode generate)
3. Test Advanced Mode with premium
4. Test copy to clipboard

**Expected Result:**
- ✅ UI renders correctly in all browsers
- ✅ Alpine.js works in all browsers
- ✅ Copy to clipboard works
- ✅ No console errors

**Actual Result:**
- Chrome: [ ] PASS / [ ] FAIL
- Firefox: [ ] PASS / [ ] FAIL
- Safari: [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 19: Mobile Responsiveness

**Objective:** Verify system works on mobile devices

**Steps:**
1. Open application on mobile device or use browser dev tools
2. Test Simple Mode
3. Test Advanced Mode
4. Test radio button selection on mobile

**Expected Result:**
- ✅ UI is responsive
- ✅ Forms are usable on mobile
- ✅ Radio buttons are tappable
- ✅ Results are readable
- ✅ Copy button works on mobile

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Notes: _______________________________________________

---

### TEST 20: Performance Test

**Objective:** Verify system handles load correctly

**Steps:**
1. Generate 10 captions in a row (different briefs)
2. Measure response times
3. Check for memory leaks
4. Check server logs for errors

**Expected Result:**
- ✅ Each generation completes in <30 seconds
- ✅ No memory leaks in browser
- ✅ No server errors
- ✅ Consistent performance

**Actual Result:**
- [ ] PASS / [ ] FAIL
- Average response time: _____ seconds
- Notes: _______________________________________________

---

## 📊 Test Summary

### Results:
- Total Tests: 20
- Passed: _____ / 20
- Failed: _____ / 20
- Pass Rate: _____ %

### Critical Issues Found:
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Minor Issues Found:
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Recommendations:
1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

---

## ✅ Sign-Off

**Tested By:** _____________________  
**Date:** _____________________  
**Status:** [ ] APPROVED / [ ] NEEDS FIXES  

**Notes:**
_______________________________________________
_______________________________________________
_______________________________________________

---

## 🚀 Next Steps After Testing

If all tests pass:
1. [ ] Deploy to production
2. [ ] Monitor user behavior
3. [ ] Track conversion rates
4. [ ] Gather user feedback
5. [ ] Plan Phase 2 (Credit System)

If tests fail:
1. [ ] Document all issues
2. [ ] Prioritize fixes (critical vs minor)
3. [ ] Fix issues
4. [ ] Re-test
5. [ ] Repeat until all pass

---

**Good luck with testing! 🎉**
