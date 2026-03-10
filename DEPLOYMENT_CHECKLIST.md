# ✅ Deployment Checklist - Print & Check

## 📋 PRE-DEPLOYMENT

### Preparation
- [ ] Read `DEPLOYMENT_GUIDE_PRODUCTION.md`
- [ ] Read `READY_TO_DEPLOY.md`
- [ ] Test all features in local environment
- [ ] Commit all changes to Git
- [ ] Push to remote repository

### Configuration
- [ ] Edit `deploy.sh` - Update `DB_USER`
- [ ] Edit `deploy.sh` - Update `DB_NAME` (if different)
- [ ] Verify Git remote is correct: `git remote -v`
- [ ] Verify SSH access to VPS

### Backup
- [ ] Backup production database
- [ ] Backup `.env` file
- [ ] Backup important files (optional)
- [ ] Verify backup files are readable
- [ ] Note backup file names/locations

---

## 🚀 DEPLOYMENT

### Step 1: Access Server
- [ ] SSH into VPS: `ssh user@your-vps-ip`
- [ ] Navigate to project: `cd /var/www/pintar-menulis`
- [ ] Check current branch: `git branch`

### Step 2: Execute Deployment
- [ ] Run: `bash deploy.sh`
- [ ] Watch for errors during execution
- [ ] Wait for completion message

### Step 3: Alternative Manual Deployment (if script fails)
- [ ] `php artisan down --refresh=15`
- [ ] `git pull origin main`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --class=TrendingHashtagSeeder --force`
- [ ] `php artisan cache:clear`
- [ ] `php artisan config:clear`
- [ ] `php artisan route:clear`
- [ ] `php artisan view:clear`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan optimize`
- [ ] `php artisan up`

---

## ✅ VERIFICATION

### Database Verification
- [ ] Check hashtag count: `php artisan tinker --execute="echo App\Models\TrendingHashtag::count();"`
- [ ] Expected result: `96`
- [ ] Check blacklist table exists: `php artisan tinker --execute="echo App\Models\HashtagBlacklist::count();"`
- [ ] Expected result: `0` (or any number, no error)

### Feature Testing
- [ ] Open website: `https://your-domain.com`
- [ ] Navigate to AI Generator: `/ai-generator`
- [ ] Test Simple Mode:
  - [ ] Click "Mode Simpel"
  - [ ] Check dropdown shows 23 categories
  - [ ] Select a category
  - [ ] Check subcategory dropdown appears
  - [ ] Fill in all fields
  - [ ] Generate caption
  - [ ] Verify caption generated successfully
- [ ] Test Advanced Mode:
  - [ ] Click "Mode Lanjutan"
  - [ ] Select category and subcategory
  - [ ] Fill in all fields
  - [ ] Generate caption
  - [ ] Verify caption generated successfully
- [ ] Test Hashtag System:
  - [ ] Enable "Auto Hashtag" toggle
  - [ ] Generate caption
  - [ ] Verify hashtags appear in result
  - [ ] Verify hashtags are relevant

### Schedule Verification
- [ ] Check schedule: `php artisan schedule:list`
- [ ] Verify "hashtags-weekly-update" exists (Sunday 4 AM)
- [ ] Verify "hashtags-monthly-update" exists (1st of month 5 AM)

### Log Verification
- [ ] Check for errors: `tail -n 100 storage/logs/laravel.log | grep ERROR`
- [ ] Expected result: (empty or no critical errors)
- [ ] Check general logs: `tail -n 50 storage/logs/laravel.log`
- [ ] Verify no suspicious activity

### Performance Check
- [ ] Test page load speed (should be <3 seconds)
- [ ] Check server CPU: `top` or `htop`
- [ ] Check memory usage: `free -m`
- [ ] Check disk space: `df -h`

---

## 📊 POST-DEPLOYMENT (First Hour)

### Immediate Monitoring
- [ ] Monitor logs: `tail -f storage/logs/laravel.log`
- [ ] Watch for 5-10 minutes
- [ ] Check for any errors
- [ ] Test caption generation 3-5 times
- [ ] Verify all generations successful

### User Testing
- [ ] Test from different device/browser
- [ ] Test Simple Mode (2-3 captions)
- [ ] Test Advanced Mode (2-3 captions)
- [ ] Test different categories
- [ ] Test different platforms
- [ ] Verify hashtags working

### Documentation
- [ ] Note deployment time
- [ ] Note any issues encountered
- [ ] Note backup file locations
- [ ] Update team/stakeholders

---

## 🔍 24-HOUR MONITORING

### Hour 1-6
- [ ] Check logs every hour: `tail -n 50 storage/logs/laravel.log | grep ERROR`
- [ ] Monitor user activity
- [ ] Test features randomly

### Hour 6-12
- [ ] Check logs every 2 hours
- [ ] Review caption generation stats
- [ ] Check server resources

### Hour 12-24
- [ ] Check logs every 4 hours
- [ ] Review daily stats
- [ ] Verify schedule ready for next run

---

## 📅 ONGOING MAINTENANCE

### Daily (First Week)
- [ ] Check error logs
- [ ] Verify hashtag count (96)
- [ ] Test caption generation
- [ ] Monitor server resources

### Weekly
- [ ] Review hashtag update logs (Monday after Sunday update)
- [ ] Check weekly stats
- [ ] Test all features
- [ ] Review performance

### Monthly
- [ ] Review monthly update logs (2nd of month)
- [ ] Check for blacklisted hashtags
- [ ] Review trending hashtags performance
- [ ] Plan improvements

---

## 🚨 ROLLBACK (If Needed)

### Quick Rollback Steps
- [ ] Enable maintenance: `php artisan down`
- [ ] Restore database: `mysql -u username -p pintar_menulis < backup_file.sql`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Disable maintenance: `php artisan up`
- [ ] Test website
- [ ] Document what went wrong

### Git Rollback (If Needed)
- [ ] Check commit history: `git log --oneline`
- [ ] Note current commit hash
- [ ] Rollback: `git reset --hard PREVIOUS_COMMIT_HASH`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test website
- [ ] Document issue

---

## ✅ SUCCESS CRITERIA

Deployment is successful if ALL of these are true:
- ✅ No critical errors in logs
- ✅ 96 hashtags in database
- ✅ Simple Mode shows 23 categories
- ✅ Advanced Mode working
- ✅ Hashtag generation working
- ✅ Caption generation working
- ✅ Schedule configured correctly
- ✅ No performance degradation
- ✅ Users can access all features
- ✅ Server resources normal

---

## 📞 EMERGENCY CONTACTS

### If Critical Issue Occurs:
1. **Immediate**: `php artisan down` (maintenance mode)
2. **Check**: `tail -f storage/logs/laravel.log` (identify issue)
3. **Decide**: Fix or Rollback?
4. **Act**: Apply fix or restore backup
5. **Verify**: Test thoroughly
6. **Resume**: `php artisan up`

### Contact Developer If:
- Critical errors persist
- Data loss/corruption
- Security issues
- Performance degradation >50%
- Unable to rollback

---

## 📝 NOTES SECTION

**Deployment Date**: _______________

**Deployment Time**: _______________

**Deployed By**: _______________

**Backup File**: _______________

**Issues Encountered**:
- 
- 
- 

**Resolution**:
- 
- 
- 

**Final Status**: ⭕ Success / ⭕ Partial / ⭕ Failed

**Notes**:




---

**Print this checklist and check off items as you complete them!** ✅

**Version**: 2.0
**Date**: 2026-03-11
**Status**: Ready for Production
