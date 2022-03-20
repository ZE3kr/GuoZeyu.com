export async function onRequest({ request, waitUntil }) {
  try {
    const url = new URL(request.url)
    url.protocol = 'https:'
    url.hostname = 'matomo.tlo.xyz'
    url.pathname = '/matomo.php'
    const ip = request.headers.get('CF-Connecting-IP')
    request.headers.set('TLO-Connecting-IP', ip)
	waitUntil(fetch(url.href, request))
    
	return new Response('', { status: 204 })
  } catch (err) {
    return new Response(`${err.message}\n${err.stack}`, { status: 500 })
  }
}
