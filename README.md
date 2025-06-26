This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Installation

Run the following command to install project dependencies:

```bash
npm install --legacy-peer-deps
```

This flag resolves potential peer dependency conflicts during installation.
If installation still fails, try cleaning `node_modules` and updating the conflicted packages manually.

## Getting Started

First, run the development server:

```bash
npm run dev
# or
yarn dev
# or
pnpm dev
# or
bun dev
```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.tsx`. The page auto-updates as you edit the file.

## Linting

Run the project linter to check code style and catch common issues:

```bash
npm run lint
```

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Serving PHP Pages

If you need to test the PHP portions of this project, start a local PHP server from the project root:

```bash
php -S localhost:8000
```

Navigate to <http://localhost:8000> in your browser to access the PHP pages such as `index.php`.

## Font Awesome Assets

A copy of Font Awesome is stored under `assets/fontawesome`. The directory holds the CSS and webfont files.

To include these icons in a PHP page, add the following line within the `<head>` section:

```html
<link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
```

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/app/building-your-application/deploying) for more details.
