export async function onRequest({ request }) {
  try {
    const url = new URL(request.url)
    url.protocol = 'https:'
    url.hostname = 'matomo.tloxygen.com'
    url.pathname = '/matomo.php'
    const ip = request.headers.get('CF-Connecting-IP')
    request.headers.set('TLO-Connecting-IP', ip)
    return fetch(url.href, request, { cf: { resolveOverride: url.hostname } })
  } catch (err) {
    return new Response(`${err.message}\n${err.stack}`, { status: 500 })
  }
}
