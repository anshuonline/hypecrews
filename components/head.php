<?php
$pageTitle = $pageTitle ?? 'Hypecrews — Peak Digital Excellence';
$pageDescription = $pageDescription ?? 'Hypecrews is a premium digital brand company delivering web development, copyright protection, social media management and creative growth solutions.';
$pageKeywords = $pageKeywords ?? 'digital agency, web development, copyright protection, social media management, brand design, Hypecrews';
$canonical = $canonical ?? '';
$robots = $robots ?? 'index, follow';
$ogImage = $ogImage ?? 'https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png';
$jsonLd = $jsonLd ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1B365D">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
    <?php if (!empty($canonical)): ?><link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>"><?php endif; ?>

    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($canonical)): ?><meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>"><?php endif; ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <meta property="og:site_name" content="Hypecrews">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">

    <?php if (!empty($jsonLd)): ?>
    <script type="application/ld+json"><?php echo $jsonLd; ?></script>
    <?php endif; ?>

    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Tailwind (CDN, client-side) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: { navy: '#1B365D', blue: '#005A9C', sky: '#60C1E5', ice: '#A0E0EF', ink: '#0B1C2F', mist: '#F4F9FC' },
              primary: '#1B365D',
              secondary: '#005A9C',
              accent: '#60C1E5',
              dark: '#1B365D',
              light: '#A0E0EF'
            },
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
              heading: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif']
            }
          }
        }
      }
    </script>

    <!-- Brand system -->
    <link rel="stylesheet" href="/css/brand.css">

    <?php include __DIR__ . '/google_analytics.php'; ?>

    <!-- Client-side interaction libraries -->
    <script defer src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script defer src="/js/brand.js"></script>
</head>
