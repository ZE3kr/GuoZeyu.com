export async function onRequest({ request }) {
  try {
    const url = new URL(request.url)
    url.protocol = 'https:'
    url.hostname = 'matomo.tlo.xyz'
    url.pathname = '/matomo.js'
    const ip = request.headers.get('CF-Connecting-IP')
    request.headers.set('TLO-Connecting-IP', ip)

    return fetch(url.href, request)
  } catch (err) {
    return new Response(`${err.message}\n${err.stack}`, { status: 500 })
  }
}
