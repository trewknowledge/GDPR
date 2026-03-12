import { FlatCompat } from "@eslint/eslintrc";
import { fileURLToPath } from "url";
import path from "path";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const compat = new FlatCompat({ baseDirectory: __dirname });

export default [
	{
		ignores: ["**/*.min.js", "**/node_modules/**", "**/vendor/**", "**/dist/**"],
	},
	...compat.extends("wordpress"),
	{
		languageOptions: {
			ecmaVersion: 2015,
			sourceType: "module",
			globals: {
				window: "readonly",
				document: "readonly",
				navigator: "readonly",
				jQuery: "readonly",
				$: "readonly",
				module: "readonly",
				require: "readonly",
				process: "readonly",
			},
		},
		rules: {
			"camelcase": [1],
			"space-in-parens": [1, "always"],
			"no-trailing-spaces": [1],
			"spaced-comment": [0],
			"padded-blocks": [0],
			"prefer-template": [0],
			"max-len": [0],
			"no-else-return": [0],
			"func-names": [0],
			"object-shorthand": [0],
			"indent": ["error", "tab"],
			"space-before-function-paren": 0,
			"no-tabs": 0,
			"prefer-destructuring": 0,
			"no-undef": 0,
			"no-param-reassign": 0,
		},
	},
];
