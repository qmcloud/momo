const title = 'LeopardLive'

export default function getPageTitle(pageTitle) {
    if (pageTitle) {
        return `${pageTitle} - ${title}`
    }
    return `${title}`
}