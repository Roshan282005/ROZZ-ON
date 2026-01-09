# Fix User Data Update Issues

## Issues Identified:
1. **Manual Login**: login.php authenticates but doesn't update last_login/login_count
2. **Google Login**: google-callback.php has syntax errors and doesn't save to database
3. **Inconsistent Updates**: Different endpoints used with varying update logic
4. **Missing Tracking**: save-user.php doesn't update login tracking for Google users

## Tasks:
- [ ] Fix login.php to update user tracking data on successful login
- [ ] Fix google-callback.php syntax errors and database integration
- [ ] Update save-user.php to include login tracking for Google users
- [ ] Test both login methods to verify database updates
- [ ] Verify database connection and table structure
