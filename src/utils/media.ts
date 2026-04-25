const escapeXml = (value: string) =>
  value
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;')

const createSvgDataUrl = (svg: string) =>
  `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`

const getInitials = (value: string) => {
  const clean = value.trim()
  if (!clean) {
    return 'T'
  }

  const parts = clean.split(/\s+/).filter(Boolean)
  if (parts.length > 1) {
    return parts
      .slice(0, 2)
      .map((part) => part[0])
      .join('')
      .toUpperCase()
  }

  return clean.slice(0, 2).toUpperCase()
}

export const resolveMediaUrl = (url: string | undefined | null, fallback: string) => {
  if (!url) {
    return fallback
  }

  if (
    url.startsWith('data:') ||
    url.startsWith('blob:') ||
    url.startsWith('/') ||
    url.startsWith('./')
  ) {
    return url
  }

  try {
    const parsed = new URL(url, window.location.origin)
    const allowedHosts = new Set([
      window.location.hostname,
      '127.0.0.1',
      'localhost'
    ])

    return allowedHosts.has(parsed.hostname) ? parsed.toString() : fallback
  } catch {
    return fallback
  }
}

export const createAvatarPlaceholder = (
  label: string,
  primary = '#1d4ed8',
  secondary = '#dbeafe'
) => {
  const initials = escapeXml(getInitials(label))
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160">
      <defs>
        <linearGradient id="avatarGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="${primary}" />
          <stop offset="100%" stop-color="${secondary}" />
        </linearGradient>
      </defs>
      <rect width="160" height="160" rx="80" fill="url(#avatarGradient)" />
      <circle cx="80" cy="62" r="28" fill="rgba(255,255,255,0.22)" />
      <path d="M38 132c9-25 28-38 42-38s33 13 42 38" fill="rgba(255,255,255,0.18)" />
      <text x="80" y="92" text-anchor="middle" font-size="28" font-family="Segoe UI, Arial, sans-serif" fill="#ffffff" font-weight="700">
        ${initials}
      </text>
    </svg>
  `

  return createSvgDataUrl(svg)
}

export const createCardPlaceholder = (
  title: string,
  accent = '#2563eb',
  background = '#dbeafe'
) => {
  const safeTitle = escapeXml(title.toUpperCase())
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="720" viewBox="0 0 1200 720">
      <rect width="1200" height="720" fill="${background}" />
      <rect x="54" y="54" width="1092" height="612" rx="40" fill="#ffffff" opacity="0.74" />
      <rect x="90" y="90" width="120" height="12" rx="6" fill="${accent}" opacity="0.4" />
      <rect x="90" y="122" width="260" height="12" rx="6" fill="${accent}" opacity="0.22" />
      <circle cx="972" cy="156" r="110" fill="${accent}" opacity="0.10" />
      <circle cx="1030" cy="580" r="160" fill="${accent}" opacity="0.08" />
      <rect x="90" y="470" width="360" height="18" rx="9" fill="${accent}" opacity="0.18" />
      <rect x="90" y="512" width="520" height="18" rx="9" fill="${accent}" opacity="0.12" />
      <text x="90" y="400" font-size="84" font-family="Segoe UI, Arial, sans-serif" fill="${accent}" font-weight="800">
        ${safeTitle}
      </text>
    </svg>
  `

  return createSvgDataUrl(svg)
}

export const createBannerPlaceholder = (title: string, subtitle: string) => {
  const safeTitle = escapeXml(title)
  const safeSubtitle = escapeXml(subtitle)
  const svg = `
    <svg xmlns="http://www.w3.org/2000/svg" width="1600" height="600" viewBox="0 0 1600 600">
      <defs>
        <linearGradient id="bannerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#0f172a" />
          <stop offset="55%" stop-color="#1d4ed8" />
          <stop offset="100%" stop-color="#0ea5e9" />
        </linearGradient>
      </defs>
      <rect width="1600" height="600" fill="url(#bannerGradient)" />
      <circle cx="1280" cy="120" r="160" fill="rgba(255,255,255,0.10)" />
      <circle cx="1360" cy="480" r="220" fill="rgba(255,255,255,0.08)" />
      <rect x="96" y="108" width="148" height="10" rx="5" fill="rgba(255,255,255,0.4)" />
      <rect x="96" y="138" width="260" height="10" rx="5" fill="rgba(255,255,255,0.22)" />
      <text x="96" y="310" font-size="84" font-family="Segoe UI, Arial, sans-serif" fill="#ffffff" font-weight="800">
        ${safeTitle}
      </text>
      <text x="96" y="382" font-size="30" font-family="Segoe UI, Arial, sans-serif" fill="rgba(255,255,255,0.88)">
        ${safeSubtitle}
      </text>
    </svg>
  `

  return createSvgDataUrl(svg)
}

export const mdEditorSafeProps = {
  noHighlight: true,
  noKatex: true,
  noMermaid: true,
  noIconfont: true
} as const
