const PATTERNS = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    phone: /^(?:\+92[-\s]?|0)3[0-9]{2}[-\s]?[0-9]{7}$/,
    postal: /^[0-9]{4,5}$/,
};

const PASSWORD_CHECKS = [
    [/.{8,}/, 'at least 8 characters'],
    [/[a-z]/, 'a lowercase letter'],
    [/[A-Z]/, 'an uppercase letter'],
    [/[0-9]/, 'a number'],
    [/[^A-Za-z0-9]/, 'a symbol'],
];

const RULES = {
    required: (value) => (value !== null && value !== undefined && String(value).trim() !== '') || 'This field is required.',
    email: (value) => !value || PATTERNS.email.test(String(value).trim()) || 'Please enter a valid email address.',
    phone: (value) => !value || PATTERNS.phone.test(String(value).trim()) || 'Please enter a valid Pakistani phone number, e.g. 0300 1234567.',
    postal: (value) => !value || PATTERNS.postal.test(String(value).trim()) || 'Please enter a valid postal code.',
    password: (value) => {
        if (!value) {
            return true;
        }

        const missing = PASSWORD_CHECKS.filter(([pattern]) => !pattern.test(value)).map(([, label]) => label);

        return missing.length === 0 || `Password must contain ${missing.join(', ')}.`;
    },
};

/**
 * Registers a `formValidator` Alpine component that mirrors the backend's
 * Form Request rules in the browser, so users see mistakes before the round
 * trip instead of only after a POST. The server-side rules remain the
 * source of truth — this is UX only, never a substitute for them.
 *
 * `fields` shape: [{ name, label, rules: ['required', 'email', ...], matches: 'otherFieldName' }]
 * `rules` entries are keys into RULES, or a function (value, values) => true | string.
 */
export function registerFormValidator(Alpine) {
    Alpine.data('formValidator', (fields, initial = {}) => ({
        values: Object.fromEntries(fields.map((field) => [field.name, initial[field.name] ?? ''])),
        errors: {},

        validateField(field) {
            const value = this.values[field.name];

            for (const rule of field.rules) {
                const check = typeof rule === 'function' ? rule : RULES[rule];
                const result = check(value, this.values);

                if (result !== true) {
                    this.errors[field.name] = result;

                    return false;
                }
            }

            if (field.matches) {
                const other = fields.find((f) => f.name === field.matches);

                if (other && value !== this.values[field.matches]) {
                    this.errors[field.name] = `Must match ${other.label ?? field.matches}.`;

                    return false;
                }
            }

            delete this.errors[field.name];

            return true;
        },

        validateAll() {
            let valid = true;

            fields.forEach((field) => {
                if (!this.validateField(field)) {
                    valid = false;
                }
            });

            return valid;
        },

        passwordChecklist(fieldName) {
            const value = this.values[fieldName] ?? '';

            return PASSWORD_CHECKS.map(([pattern, label]) => ({
                label,
                met: pattern.test(value),
            }));
        },

        onSubmit(event) {
            if (!this.validateAll()) {
                event.preventDefault();

                this.$nextTick(() => {
                    const firstInvalid = fields.find((field) => this.errors[field.name]);
                    const el = firstInvalid && document.getElementById(firstInvalid.id ?? firstInvalid.name);
                    el?.focus();
                });
            }
        },
    }));
}
