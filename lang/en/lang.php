<?php

declare(strict_types=1);

return [
    'menu' => [
        'fortify' => 'Fortify',
    ],
    'permissions' => [
        'app_fortify' => 'Fortify',
    ],
    'tabs' => [
        'config' => 'Config',
        'ip_firewall' => 'IP Firewall',
        'fortify' => 'Fortify',
        'tests' => 'Tests',
        'csp' => 'CSP',
        'cors' => 'CORS',
        'input_sanitizer' => 'Input Sanitizer',
    ],
    'messages' => [
        'fortify_description' => 'Setting up the application protection system',
    ],
    'widgets' => [
        'fortify' => 'Fortify',
    ],
    'buttons' => [
        'edit' => 'Edit',
        'run_test' => 'Run test',
        'install_plugin' => 'Install Plugin',
        'enable_plugin' => 'Enable Plugin',
        'view' => 'View',
        'update' => 'Update',
    ],
    'comments' => [
        'plugin_is_not_installed' => 'To use this feature, you need to install the plugin.',
        'plugin_is_not_enabled' => 'To use this feature, you need to enable the plugin.',
        'session_same_site' => 'This option determines how your cookies behave when cross-site requests take place,
        and can be used to mitigate CSRF attacks. By default, we do not enable this as other CSRF protection services
        are in place. <br> In the strict mode, the cookie is not sent with any cross-site usage even if the user
        follows a link to another website. Lax cookies are only sent with a top-level get request.',
        'session_secure' => 'By setting this option to true, session cookies will only be sent back to the server
        if the browser has a HTTPS connection. This will keep the cookie from being sent to you if it can not be
        done securely.',
        'session_http_only' => 'Setting this value to true will prevent JavaScript from accessing the value of the
         cookie and the cookie will only be accessible through the HTTP protocol. You are free to modify this
         option if needed.',
        'session_encrypt' => 'This option allows you to easily specify that all of your session data should be
         encrypted before it is stored. All encryption will be run automatically by Laravel and you can use the Session
         like normal.',
        'session_lifetime' => 'Here you may specify the number of minutes that you wish the session to be allowed to
        remain idle for it is expired. If you want them to immediately expire when the browser closes, set it to zero.',
        'password_policy_section' => 'Specify the password policy for backend administrators.',
        'config_enabled' => '<b>When enabled, all current settings will be applied. Previous settings will be
        overwritten by the current ones.</b>',
        'admin_ip_access_enabled' => '<b>When enabled, access to the admin panel is limited by the IP whitelist.</b>',
        'ip_blocker_enabled' => '<b>When enabled, access to CMS for these IPs will be limited.</b>',
        'backend_force_secure' => 'Use this setting to force a secure protocol when accessing any backend pages,
        including the authentication pages. This is usually handled by web server config, but can be handled by the app
        for added security.',
        'backend_force_single_session' => 'Use this setting to prevent concurrent sessions. When enabled, backend
        users cannot sign in to multiple devices at the same time. When a new sign in occurs, all other sessions for
        that user are invalidated.',
        'admin_ip_access_view' => 'The code of the page that will be displayed when the admin panel is unavailable.',
        'smart_ip_blocker_view' => 'The code of the page that will be displayed when the admin panel is unavailable.',
        'ip_blocker_view' => 'The code of the page that will be displayed when CMS is unavailable.',
        'input_sanitizer_view' => 'The page shown when the request contains invalid input.',
        'admin_ip_access_ips' => 'The admin panel will only be accessible from the <b>static IPs</b>
        listed. <b>Please make sure your IP is included in the list</b>.',
        'ip_blocker_ips' => 'Access to the application will be limited for these IPs. <b>Please make sure
        your IP is not in the list</b>.',
        'admin_ip_access_ips_example' => 'For example: <b>192.168.1.1</b>',
        'ip_blocker_ips_example' => 'For example: <b>192.168.1.1</b>',
        'sensitive_files_checker_section' => 'This function scans the specified OctoberCMS site for potentially
         sensitive files that should not be publicly accessible, such as <b>.env, auth.json, composer.json</b>,
         and other configuration or secret files. The scan checks whether these files can be reached via HTTP, helping
         to identify security risks.',
        'sensitive_files_checker_urls' => '<b>Check the list of URLs</b> and make sure your domain is included so the
        scan targets your site correctly.',
        'sensitive_files_checker_empty_urls' => 'The list of URLs to check for sensitive files is empty. Please add at
        least one URL to run the test.',
        'sensitive_files_checker_urls_example' => 'For example: <b>https://example.com</b>',
        'sensitive_files_checker_test_success' => 'No sensitive files are publicly accessible via HTTP.',
        'sensitive_files_checker_test_failed' => 'Some sensitive files (:number) appear to be publicly accessible via HTTP. These
        files may contain confidential information such as environment variables, credentials, internal configuration,
        or project dependencies. Exposing such files may allow attackers to gain insight into the application structure
        or access sensitive data.',
        'sensitive_tcp_ports_checker_ips_example' => '<p><b>IP</b> – Enter the IP. Examples:
        <b>192.168.1.1</b></p><p><b>Ports</b> – Enter the port numbers you want to check, separated by
        commas. Example: <b>21,22,8443,8080,9200</b></p>',
        'sensitive_tcp_ports_checker_ips' => '<b>Check the list of IPs and ports</b> and make sure the important
        ones are included so the scan targets your server correctly.',
        'sensitive_tcp_ports_checker_section' => 'This function scans the specified server for potentially sensitive or
        commonly targeted ports that should not be publicly accessible, such as <b>SSH (22)</b>, <b>FTP (21)</b>,
        <b>database ports (3306, 5432)</b>, <b>Redis (6379)</b>, and other critical services. The scan checks whether
        these ports are open, helping to identify security risks and misconfigurations.',
        'sensitive_tcp_ports_checker_empty_ips' => 'The list of IPs and ports to check for sensitive services is empty. Please add at
        least one IP and port to run the test.',
        'sensitive_tcp_ports_checker_test_success' => 'No sensitive ports are publicly accessible on the specified IPs.',
        'sensitive_tcp_ports_checker_test_failed' => 'Some sensitive ports (:number) appear to be publicly accessible on the specified IPs. These
        ports may expose critical services such as SSH, FTP, databases, or Redis. Leaving such ports
        open to the public may allow attackers to gain insight into the server configuration
        or access sensitive data.',
        'ssl_certificate_checker_hosts_example' => '<p><b>Host</b> – Enter the host. Examples:
        <b>example.com</b></p><p><b>Ports</b> – Enter the port numbers you want to check, separated by
        commas. Example: <b>443,465,993,995</b></p>',
        'ssl_certificate_checker_section' => 'SSL Certificate Checker allows you to monitor SSL/TLS certificates for
        your domains. It shows the issuer, validity period, days left until expiration, and whether the certificate has
        expired.',
        'ssl_certificate_checker_hosts' => '<b>Check the list of hosts and ports</b> and make sure the important
        ones are included so the scan targets your server correctly.',
        'ssl_certificate_checker_empty_hosts' => 'The list of hosts and ports to check for sensitive services is empty.
        Please add at least one host and port to run the test.',
        'ssl_certificate_checker_test_success' => 'The SSL/TLS certificates for the specified hosts are valid and
        properly configured.',
        'ssl_certificate_checker_test_failed' => 'Some SSL/TLS certificates (:number) for the specified host appear
        to be invalid, expired, or improperly configured. This may expose users to security risks such as data
        interception or man-in-the-middle attacks. It is recommended to install valid certificates issued by a trusted
        certificate authority and ensure they are correctly configured.',
        'sensitive_files_checker_paths_example' => 'For example: <b>.env</b>, <b>storage/logs</b>',
        'smart_ip_blocker_requests_limit' => 'Maximum number of requests allowed from a single IP per minute before blocking.',
        'smart_ip_blocker_ban_hours' => 'Duration to block the IP after exceeding the limit.',
        'smart_ip_blocker_cache_control' => 'Enable or disable automatic cleanup of expired IPs from the cache',
        'smart_ip_blocker_excluded_ips_example' => 'For example: <b>192.168.1.1</b>',
        'smart_ip_blocker_excluded_ips' => '<b>IPs</b> that should never be blocked by this system.',
        'smart_ip_blocker_enabled' => 'When enabled, <b>requests</b> exceeding the rate limit will be blocked by <b>IP</b>.',
        'smart_ip_blocker_excluded_headers_example' => '<p><b>Header</b> – Enter the header. For example:
        <b>User-Agent</b></p><p><b>Value</b> – Enter the header value. For example: <b>Googlebot</b></p>',
        'smart_ip_blocker_excluded_headers' => '<b>Headers</b> that should never be blocked by this system.',
        'csp_cms_enabled' => 'Enable these CSP settings to activate content security protection. This setting affects only the site’s front-end, ensuring security and proper behavior for visitors.',
        'cors_cms_enabled' => 'Enable these CORS settings to control which external domains can access your resources. This setting affects only the site’s front-end, ensuring security and proper behavior for visitors.',
        'input_sanitizer_cms_enabled' => 'Turn on to enforce safe and clean input from users. This setting affects only the site’s front-end, ensuring security and proper behavior for visitors.',
        'csp_description' => '<h2>Content Security Policy (CSP)</h2>
        <p>
            Content Security Policy (CSP) is a security standard that helps prevent
            cross-site scripting (XSS), clickjacking, and other code injection attacks
            by specifying which sources of content are allowed to load in the browser.
        </p>
        <p>
            For detailed information, see the official documentation:
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP" target="_blank">
                MDN Web Docs: Content Security Policy (CSP)
            </a>.
        </p>
        <h3>Available Directives</h3>
        <ul>
            <li><strong>default-src</strong> — Default source for all content types if not explicitly specified.</li>
            <li><strong>style-src</strong> — Allowed sources for CSS stylesheets.</li>
            <li><strong>img-src</strong> — Allowed sources for images.</li>
            <li><strong>font-src</strong> — Allowed sources for fonts.</li>
            <li><strong>connect-src</strong> — Allowed sources for AJAX, WebSocket, and other connections.</li>
            <li><strong>media-src</strong> — Allowed sources for audio and video.</li>
            <li><strong>frame-src</strong> — Allowed sources for iframe content.</li>
            <li><strong>object-src</strong> — Allowed sources for &lt;object&gt;, &lt;embed&gt;, and &lt;applet&gt;.</li>
            <li><strong>base-uri</strong> — Restricts allowed &lt;base&gt; tag URLs.</li>
            <li><strong>form-action</strong> — Specifies valid URLs where forms can be submitted.</li>
            <li><strong>frame-ancestors</strong> — Specifies which sites are allowed to embed this page in an iframe.</li>
        </ul>
        <h3>Example Values for Directives</h3>
        <ul>
            <li><strong>\'self\'</strong> — Only allows content from the current origin.</li>
            <li><strong>\'none\'</strong> — Disallows all sources.</li>
            <li><strong>https://example.com</strong> — Allows content from a specific domain.</li>
            <li><strong>data:</strong> — Allows inline content encoded as data URIs.</li>
            <li><strong>https://cdn.example.com</strong> — Allows content from a CDN or external host.</li>
        </ul>
        <p>
            By configuring CSP, you can significantly improve your website\'s security
            by controlling what content can be loaded and executed.
        </p><p><strong>Warning:</strong> Your CSP header can be overwritten by other middleware or server settings. Make sure to configure it centrally to avoid conflicts.</p>',
        'cors_description' => '
        <h2>Cross-Origin Resource Sharing (CORS)</h2>
        <p>
            Cross-Origin Resource Sharing (CORS) is a security feature that allows you to specify
            which external domains are permitted to access your website\'s resources.
            This helps prevent unauthorized websites from making requests to your server.
        </p>
        <p>
            For more details, see the official documentation:
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS" target="_blank">
        MDN Web Docs: Cross-Origin Resource Sharing (CORS)
        </a>.
        </p>
        <h3>Common Settings</h3>
        <ul>
            <li><strong>Access-Control-Allow-Origin</strong> — Specifies which domains can access your resources. Examples: <code>*</code> (all domains), <code>https://example.com</code> (specific domain).</li>
            <li><strong>Access-Control-Allow-Methods</strong> — Specifies allowed HTTP methods. Examples: <code>GET, POST, PUT</code>.</li>
            <li><strong>Access-Control-Allow-Headers</strong> — Specifies allowed headers in requests. Examples: <code>Content-Type, Authorization</code>.</li>
            <li><strong>Access-Control-Allow-Credentials</strong> — Indicates whether cookies or authentication data can be sent. Examples: <code>true</code> or <code>false</code>.</li>
            <li><strong>Access-Control-Expose-Headers</strong> — Specifies which headers are accessible to the client. Example: <code>X-Custom-Header</code>.</li>
            <li><strong>Access-Control-Max-Age</strong> — Defines how long the results of a preflight request can be cached. Example: <code>3600</code> seconds.</li>
        </ul>
        <p>
            By configuring CORS correctly, you can safely share resources with other domains
            while preventing unauthorized access and cross-site attacks.
        </p>
        <p><strong>Warning:</strong> Your CORS header can be overwritten by other middleware or server settings. Make sure to configure it centrally to avoid conflicts.</p>
        ',
        'block_threshold' => 'Lower values mean stricter protection (more requests will be blocked).',
        'input_sanitizer_excluded_headers' => '<b>Headers</b> that should never be checked by this system. Any header with the specified name will be excluded from validation.',
        'input_sanitizer_excluded_headers_example' => 'For example: <b>User-Agent</b>',
        'input_sanitizer_excluded_inputs' => '<b>Input parameters</b> that should never be checked by this system. Any input parameter with the specified name will be excluded from validation.',
        'input_sanitizer_excluded_inputs_example' => 'For example: <b>input_parameter</b>',
        'xss_patterns' => 'Detects common cross-site scripting (XSS) payloads such as <script\> tags, dangerous HTML elements, inline event handlers, and JavaScript/VBScript URIs. Detection is performed using regular expressions.',
        'encoded_xss_patterns' => 'Detects encoded XSS payloads (e.g. HTML entities or URL-encoded <script> tags). Detection is performed using regular expressions.',
        'command_injection_patterns' => 'Detects attempts to execute system commands (e.g. cmd, bash, curl, pipes, backticks, subshells). Detection is performed using regular expressions.',
        'path_traversal_patterns' => 'Detects directory traversal attempts (e.g. ../, encoded paths, access to system files). Detection is performed using regular expressions.',
        'ssti_patterns' => 'Detects Server-Side Template Injection payloads (e.g. {{ }}, {% %}, {!! !!}). Detection is performed using regular expressions.',
        'null_byte_patterns' => 'Detects null byte injection attempts (\x00, %00, etc.). Detection is performed using regular expressions.',
        'csv_injection_patterns' => 'Detects potentially dangerous CSV values starting with special characters like =, +, -, or @. Detection is performed using regular expressions.',
        'xss_patterns_example' => 'For example: ~<\s*(script|iframe|object|embed|svg|meta|base|form|input|button)\b|javascript\s*:|vbscript\s*:|data\s*:\s*text/html|<[^>]+?\s+on[a-z]{3,30}\s*=~ix',
        'encoded_xss_patterns_example' => 'For example: ~(&lt;|%3c|%253c)\s*script~ix',
        'command_injection_patterns_example' => 'For example: ~(?:^|[;&\s])\s*(cmd|powershell|bash|sh|curl|wget|nc)\b|[a-z0-9]\s*\|\s*[a-z0-9]|&&|`[^`]+`|\$\([^)]*\)~ix',
        'path_traversal_patterns_example' => 'For example: ~\.\./|\.\.\\\\|%2e%2e%2f|%2e%2e%5c|/etc/passwd|windows/system32~ix',
        'ssti_patterns_example' => 'For example: ~\{\{.*?\}\}|\{%.*?%\}|\{!!.*?!!\}~sx',
        'null_byte_patterns_example' => 'For example: ~\x00|%00|\\\\0|\\\\x00~ix',
        'csv_injection_patterns_example' => 'For example: ~^\s*[=<$#]~x',
    ],
    'fields' => [
        'status_updates_pending' => 'Pending Software Updates',
        'xss_patterns' => 'XSS Patterns',
        'encoded_xss_patterns' => 'Encoded XSS Patterns',
        'command_injection_patterns' => 'Command Injection Patterns',
        'path_traversal_patterns' => 'Path Traversal Patterns',
        'ssti_patterns' => 'SSTI Patterns',
        'null_byte_patterns' => 'Null Byte Patterns',
        'csv_injection_patterns' => 'CSV Injection Patterns',
        'name' => 'Name',
        'block_threshold' => 'Block threshold',
        'input_sanitizer' => 'Input Sanitizer',
        'cors' => 'CORS',
        'csp' => 'CSP',
        'header' => 'Header',
        'value' => 'Value',
        'excluded_headers' => 'Excluded headers',
        'excluded_inputs' => 'Excluded inputs',
        'excluded_ips' => 'Excluded IPs',
        'modules' => 'Modules',
        'expires_on' => 'Expires on',
        'issued_on' => 'Issued on',
        'hosts' => 'Hosts',
        'host' => 'Host',
        'port' => 'Port',
        'ports' => 'Ports',
        'status' => 'Status',
        'ip' => 'IP',
        'ips' => 'IPs',
        'url' => 'URL',
        'urls' => 'URLs',
        'paths' => 'Paths',
        'path' => 'Path',
        'sensitive_files_checker' => 'Sensitive files checker',
        'sensitive_files_checker_report' => 'Sensitive files checker report',
        'ssl_certificate_checker' => 'SSL certificate checker',
        'ssl_certificate_checker_report' => 'SSL certificate checker report',
        'sensitive_tcp_ports_checker' => 'Sensitive TCP ports checker',
        'sensitive_tcp_ports_checker_report' => 'Sensitive TCP ports checker report',
        'vulnerable_number_of_outdated_administrators' => 'Number of outdated administrator accounts detected: :number',
        'vulnerable_number_of_superusers_warning' => 'The number of superusers exceeds the recommended limit, which may increase the risk of admin panel compromise. Currently detected: :number_of_superusers superusers',
        'vulnerable_number_of_superusers_success' => 'The number of superuser accounts (:number_of_superusers) does not exceed the recommended limit',
        'vulnerable_backend_uri_warning' => 'Your admin panel URI <b>:uri</b> may be vulnerable because it is commonly targeted by automated bots and scanners',
        'vulnerable_backend_uri_success' => 'The admin panel URI <b>:uri</b> appears to be unique and is not a commonly scanned path',
        'debug_mode_is_disabled' => 'Application debug mode is disabled',
        'app_production_env_is_enabled' => 'The application production environment is enabled',
        'info' => 'Info',
        'view' => 'View',
        'ip_firewall' => 'IP Firewall',
        'admin_ip_access' => 'Admin IP Access',
        'ip_blocker' => 'IP Blocker',
        'smart_ip_blocker' => 'Smart IP Blocker',
        'config' => 'Config',
        'enabled' => 'Enabled',
        'session' => 'Session',
        'backend' => 'Backend',
        'password_policy' => 'Password policy',
        'session_same_site' => 'Same-Site Cookies',
        'session_secure' => 'HTTPS Only Cookies',
        'session_http_only' => 'HTTP Access Only',
        'session_encrypt' => 'Session Encryption',
        'session_lifetime' => 'Session Lifetime',
        'password_policy_allow_reset' => 'Allow administrators to reset their own passwords via self service',
        'password_policy_require_uppercase' => 'Require at least one uppercase letter (A–Z)',
        'password_policy_require_lowercase' => 'Require at least one lowercase letter (a–z)',
        'password_policy_require_number' => 'Require at least one number',
        'password_policy_require_nonalpha' => 'Require non-alphabetic characters',
        'password_policy_expire_days' => 'Enable password expiration after number of days, false to disable',
        'password_policy_min_length' => 'Password minimum length between 4 - 128 chars',
        'backend_force_secure' => 'Force HTTPS security',
        'backend_force_single_session' => 'Force Single Session',
        'sensitive_administrator_login_checker' => 'Sensitive administrative usernames were detected. Detected usernames: <b>:logins</b>',
        'smart_ip_blocker_requests_limit' => 'Requests per minute',
        'smart_ip_blocker_ban_hours' => 'Ban duration (hours)',
        'smart_ip_blocker_cache_control' => 'Cache control',
    ],
    'errors' => [
        'access_denied_title' => 'Access denied',
        'access_denied_message' => 'Sorry, this page is not accessible to you.',
        'bad_request_title' => 'Something went wrong',
        'bad_request_message' => 'Please check your input and try again.',
        'error' => 'Error',
        'widget_action_error' => 'Something went wrong while performing the action. <b>Error: :error</b>',
        'openssl_is_not_installed' => 'The OpenSSL PHP extension is required to perform SSL/TLS certificate checks. Please enable it in your PHP configuration.',
    ],
    'validator_rules' => [
        'admin_ip_access_current_ip' => 'Your current IP (:ip) must be included in the whitelist. Please add it to the list.',
        'smart_ip_blocker_current_ip' => 'Your current IP (:ip) must be included in the whitelist. Please add it to the list.',
        'ip_blocker_current_ip' => 'Your current IP (:ip) is in the blacklist. Please remove it from the list.',
    ],
];
