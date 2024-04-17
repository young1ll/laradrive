
/**
 * 지정된 url로 GET 요청을 수행합니다.
 * 
 * Inertia.js를 사용하는 경우, frontend에서 직접 fetch 메서드를 사용하는 것을 피하기 위해 사용할 수 있습니다.
 * 예를 들어, 페이지 전체를 불러오기 보다 데이터만 json으로 불러오기 원하는 경우,
 * controller에 다시 get request를 보내기 위해 사용할 수 있습니다.
 * 따라서, httpGet 메서드를 사용하면, fetch를 직접 사용하지 않고 json을 요청할 수 있으며,
 * url을 변경하지 않고 data만 요청할 수 있습니다.
 * 
 * @param {*} url 
 * @returns Promise(json)
 */
export function httpGet(url) {
    return fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => response.json());
}

export function httpPost(url, token, data) {
    return fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        method: 'POST',
        body: JSON.stringify(data)
    }).then(response => {
        if(response.ok) {
            return response.json();
        } else {
            throw new Error(response.statusText);
        }
    });
}