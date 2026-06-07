# Fix Log

## [2026-05-01] tailwind.config.js ReferenceError: require is not defined
- **Issue**: Tailwind configuration was using CommonJS `require()` while the project is configured as ES Module (`"type": "module"` in `package.json`).
- **Solution**: Converted `require` statements to `import` in `tailwind.config.js`.
- **Status**: Fixed. `npm run dev` now runs successfully.
