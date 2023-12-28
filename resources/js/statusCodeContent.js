export default {

    404: {
        description:
            "This error occurs when the requested page or resource is not found on the server.",
        action: "Identify and fix broken links, update URLs, and ensure proper redirection to valid pages.",
    },

    500: {
        description:
            "A generic server error indicating a problem with the server's configuration or an unexpected condition.",
        action: "Debug server-side code, optimize databases, and closely monitor server resources for potential issues.",
    },

    403: {
        description:
            "Access to the requested resource is forbidden, typically due to insufficient permissions.",
        action: "Review and adjust permissions, block unauthorized access attempts, and strengthen user validation processes.",
    },

    401: {
        description:
            "Access to the resource is denied due to missing or incorrect authentication credentials.",
        action: "Ensure proper authentication mechanisms are in place, and users provide valid credentials.",
    },

    400: {
        description:
            "The server cannot process the request due to malformed syntax or invalid parameters.",
        action: "Check and validate request parameters, ensuring adherence to the expected syntax.",
    },

    503: {
        description:
            "The server is temporarily unable to handle the request, often due to maintenance or overload.",
        action: "Monitor server load, scale resources as needed, and provide informative maintenance pages during planned downtime.",
    },

    429: {
        description:
            "The client has exceeded the rate limit or quota allowed for the requested resource.",
        action: "Implement rate limiting strategies, optimize resource usage, and provide clear communication on rate limits.",
    },

    502: {
        description:
            "The server, while acting as a gateway or proxy, received an invalid response from an upstream server.",
        action: "Investigate the communication between servers, check upstream server health, and address any connectivity issues.",
    },

    504: {
        description:
            "The server, while acting as a gateway or proxy, did not receive a timely response from an upstream server.",
        action: "Investigate and optimize the response time of upstream servers, and consider adjusting timeout settings.",
    },

    415: {
        description:
            "The server cannot process the request because the media type specified in the request's headers is not supported.",
        action: "Ensure that the server supports the specified media type or adjust the request headers accordingly.",
    },

    408: {
        description:
            " The server timed out waiting for the request from the client.",
        action: " Investigate and address factors contributing to prolonged request processing times, such as slow database queries or resource bottlenecks.",
    },

    501: {
        description:
            " The server does not support the functionality required to fulfill the request.",
        action: "Determine if the requested functionality is necessary, and if so, implement the required server-side features.",
    },

    503: {
        description:
            "The server is temporarily unable to handle the request due to overloading or maintenance of the server.",
        action: " Scale resources as needed, optimize server performance, and communicate maintenance schedules to users.",
    },

    505: {
        description:
            "The server does not support the HTTP protocol version used in the request.",
        action: "Ensure compatibility by updating the HTTP protocol version used or implementing support for the specified version.",
    },

    418: {
        description:
            " This status code is a joke and indicates that the server refuses to brew coffee because it is a teapot.",
        action: "While this status code is not intended for serious use, it serves as a humorous reminder to handle unexpected or unsupported requests appropriately.",
    },

    411: {
        description:
            "The server refuses to accept the request without a valid Content-Length header.",
        action: "Ensure that requests requiring a content body include a valid Content-Length header.",
    },

    413: {
        description:
            "The server rejects the request because the payload is larger than the server is willing or able to process.",
        action: "Optimize payload size, consider chunked transfer encoding, or increase server capacity to handle larger payloads.",
    },

    414: {
        description:
            "The server rejects the request because the URI (Uniform Resource Identifier) is longer than the server can interpret.",
        action: " Shorten or restructure URIs to be within acceptable length limits.",
    },

    431: {
        description:
            " The server rejects the request because the sum of all header fields is too large.",
        action: " Optimize headers, reduce redundant or unnecessary information, and ensure compliance with server limits.",
    },

    444: {
        description:
            " A non-standard status code indicating that the server has returned no information and closed the connection.",
        action: " Investigate the server and network conditions to identify and address any issues causing abrupt connection closures.",
    },

    450: {
        description:
            " This status code is not a standard HTTP status code but is sometimes used by Microsoft servers to indicate that the request is blocked by parental controls.",
        action: "Confirm the existence of parental controls, and adjust settings as needed. For non-Microsoft scenarios, consider alternative approaches.",
    },

    508: {
        description:
            "The server detects an infinite loop while processing a request.",
        action: "Review the application's logic and correct the loop or add safeguards to prevent infinite loops.",
    },

    511: {
        description:
            " The client needs to authenticate to gain network access.",
        action: "Implement network authentication mechanisms and ensure clients provide valid credentials for network access.",
    },

    522: {
        description:
            " The server did not receive a timely response from another server while attempting to load a webpage.",
        action: " Investigate and address connectivity issues, server load, and potential timeouts in the communication between servers.",
    },

    523: {
        description:
            "  The server is unable to reach the origin server while attempting to load a webpage.",
        action: " Verify the availability and accessibility of the origin server, check DNS configurations, and address any network issues.",
    },

    524: {
        description:
            " A timeout occurred on the server while waiting for a response from another server.",
        action: "Investigate and optimize server-to-server communication, adjust timeout settings, and address any network or resource bottlenecks.",
    },

    525: {
        description:
            "The SSL/TLS handshake between the client and the server failed.",
        action: "Review SSL/TLS configurations, certificates, and ensure compatibility between client and server encryption protocols.",
    },

    526: {
        description:
            " The server returns this status code when the SSL certificate is not valid.",
        action: " Ensure the SSL certificate is valid, properly configured, and has not expired. Renew or update the certificate as needed.",
    },

    520: {
        description:
            " A non-standard status code indicating an unknown server error or an error that is not otherwise classified.",
        action: "Investigate server logs, monitor server health, and address any issues contributing to unknown errors.",
    },

    530: {
        description:
            "A non-standard status code indicating that the requested website has been frozen or temporarily taken offline.",
        action: "Investigate the reason for the website freeze, and take appropriate actions based on the circumstances.",
    },

    598: {
        description:
            "This status code is not a standard HTTP status code but is sometimes used to indicate a network read timeout.",
        action: "Investigate network conditions, optimize server performance, and adjust timeout settings as necessary.",
    },

    599: {
        description:
            "Similar to 598, this non-standard status code indicates a network connect timeout.",
        action: "Address network connectivity issues, optimize server-to-server communication, and adjust timeout settings.",
    },

    406: {
        description:
            " The server cannot produce a response matching the list of acceptable values defined in the request's headers.",
        action: "Review and adjust content negotiation headers to align with the server's capabilities.",
    },
};
