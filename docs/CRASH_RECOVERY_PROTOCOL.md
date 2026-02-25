# Crash Recovery Protocol

## 🚨 What to Do When Something Goes Wrong

---

## 📋 Recovery Steps

### Step 1: Identify the Issue

1. **Check the error message**
2. **Note which file/component caused the error**
3. **Identify which phase was being worked on**

---

### Step 2: Consult TODO.md

```bash
# Open TODO.md to see progress
cat docs/TODO.md
```

1. Find the last unchecked item
2. Resume from that point
3. Do NOT regenerate completed phases

---

### Step 3: Common Issues & Solutions

#### Issue: Tailwind CSS Not Working

**Symptoms:** Styles not applying, unstyled page

**Solution:**
```bash
# Clear cache and rebuild
npm run build
php artisan view:clear
php artisan cache:clear
```

**Check:**
- `vite.config.js` has correct input paths
- `resources/css/app.css` exists
- `@vite` directive is in layouts

---

#### Issue: Alpine.js Not Working

**Symptoms:** Interactivity not working, x-data not functioning

**Solution:**
```bash
# Rebuild assets
npm run build
```

**Check:**
- `resources/js/app.js` exists and imports Alpine
- `@vite` directive includes `app.js`
- No JavaScript errors in console

---

#### Issue: Theme Not Switching

**Symptoms:** Theme buttons not changing appearance

**Solution:**
```blade
<!-- Verify HTML has theme controller -->
<html x-data="themeController()">
```

**Check:**
- `theme.js` is registered in `app.js`
- CSS variables are defined in `app.css`
- Browser localStorage is not blocked

---

#### Issue: Translation Not Working

**Symptoms:** `__('key')` returns the key itself

**Solution:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
```

**Check:**
- Language files exist in `lang/en/` and `lang/id/`
- `app.locale` config is set correctly
- Session locale is set after switch

---

#### Issue: Components Not Found

**Symptoms:** `View [components.xxx] not found`

**Solution:**
```bash
# Clear view cache
php artisan view:clear
```

**Check:**
- Component file exists in `resources/views/components/`
- Component name uses kebab-case
- File extension is `.blade.php`

---

#### Issue: Routes Not Working

**Symptoms:** 404 errors on routes

**Solution:**
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache
```

**Check:**
- `routes/web.php` file exists
- Routes are defined correctly
- Controller files exist

---

### Step 4: File Structure Verification

If project structure is corrupted:

```bash
# Verify core structure
ls -la resources/views/
ls -la resources/css/
ls -la resources/js/
ls -la lang/
ls -la docs/
```

**Required Structure:**
```
resources/
├── css/app.css
├── js/
│   ├── app.js
│   ├── theme.js
│   ├── lang.js
│   └── clock.js
└── views/
    ├── components/
    ├── layouts/
    └── pages/
```

---

### Step 5: Complete Rebuild (Last Resort)

If everything is broken:

```bash
# 1. Backup current work
cp -r . ../roxwood_backup

# 2. Fresh install
rm -rf node_modules vendor
composer install
npm install

# 3. Rebuild
npm run build

# 4. Clear all caches
php artisan optimize:clear
```

---

## 🔄 Resuming Work

### After Crash

1. **Check docs/TODO.md** for progress
2. **Find last unchecked item**
3. **Resume from that phase**
4. **DO NOT repeat completed work**

### Example

```
Last completed: ✅ PHASE 4: Layout System
Next task:     ⬜ PHASE 5: Component Library

Action: Continue with component creation
```

---

## 📞 Getting Help

1. **Check docs/** - Architecture, Theme System, Component Library
2. **Review error messages** - They often point to the exact issue
3. **Verify file structure** - Use structure checklist above
4. **Clear caches** - Often fixes mysterious issues

---

## ⚠️ Prevention

### Before Making Changes

1. **Read existing code** first
2. **Understand the architecture**
3. **Follow existing patterns**
4. **Test incrementally**

### Best Practices

- ✅ Make small, testable changes
- ✅ Commit working code frequently
- ✅ Follow file structure strictly
- ✅ Use Blade Components, not raw HTML
- ✅ Use CSS Variables, not hardcoded colors

### Avoid

- ❌ Large changes without testing
- ❌ Modifying file structure
- ❌ Skipping documentation
- ❌ Hardcoding colors/values
- ❌ Mixing languages in docs

---

## 📋 Recovery Checklist

- [ ] Identified the issue
- [ ] Checked TODO.md for progress
- [ ] Tried solution for specific issue
- [ ] Cleared all caches
- [ ] Verified file structure
- [ ] Tested fixes incrementally
- [ ] Updated documentation if needed

---

*Last Updated: 2026-02-24*
