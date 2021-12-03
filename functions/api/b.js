export async function onRequestPost(request) {
  const url = new URL(request.url)
  url.protocol = 'https:'
  url.hostname = 'matomo.tloxygen.com'
  url.pathname = '/matomo.js'
  request.url = url.toString()
  return fetch(url.toString(), request, { cf: { resolveOverride: url.hostname } })
}
