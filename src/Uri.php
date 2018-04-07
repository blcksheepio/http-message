<?php
namespace BlcksheepIO\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 * @package BlcksheepIO\Http\Message
 */
class Uri implements UriInterface
{

    /**
     * Sub-delimiter characters used
     * in query strings, user info
     * and string fragments.
     *
     * @const string CHAR_SUB_DELIMS
     */
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Unreserved characters used in user info, paths,
     * query strings, and fragments.
     *
     * @const string CHAR_UNRESERVED
     */
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~\pL';

    /**
     * Simple work-around for local/generic URIs.
     *
     * HTTP(s) URIs require a valid host in order
     * to comply with RFC 7230 Section 2.7.
     * however, in the case of generic URIs,
     * the host might be empty.
     *
     * We can use this as a fallback in the case
     * that no host is provided, thereby remaining
     * RFC compliant.
     */
    const HTTP_DEFAULT_HOST = 'localhost';

    /**
     * Maps valid, allowed scheme
     * names to their default ports.
     *
     * @var int[] $allowedSchemes
     */
    protected $allowedSchemes = [
        'http'  => 80,
        'https' => 443,
    ];

    /**
     * Represents the valid
     * host portion of a Uri.
     *
     * @var string $host
     */
    protected $host = '';

    /**
     * Represents the valid
     * path of the Uri.
     *
     * @var string $path
     */
    protected $path = '';

    /**
     * The port number used
     * in the Uri instance.
     *
     * @var int $port
     */
    protected $port = null;

    /**
     * The RFC compliant
     * scheme used;
     *
     * @var string $scheme
     */
    protected $scheme = '';

    /**
     * The username/password
     * portion of the Uri.
     *
     * @var string $userInfo
     */
    protected $userInfo = '';

    /**
     * Uri constructor.
     *
     * @param string $uri
     */
    public function __construct($uri = '')
    {
        // Check to see if a valid string is passed
        $this->isValidString(__METHOD__, $uri);

        // If the string is NOT empty.. parse it
        if (!empty($uri)) {
            $this->parseUri($uri);
        }
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        if (empty($this->host)) {
            return '';
        }

        // Check to see if we need to add the $userInfo portion
        $authority = $this->host;
        if (!empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $this->host;
        }

        // Check to see if we used a non-standard port number
        // If it IS a non-standard port add the port number
        if ($this->isNonStandardPort($this->scheme, $this->host, $this->port)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        // TODO: Implement getQuery() method.
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        // TODO: Implement getFragment() method.
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        // First, check to see that the $sceme parameter is a valid string.
        // If it is not, throw an exception.
        $this->isValidString(__METHOD__, $scheme);

        // Attempt to filter out the scheme used
        $scheme = $this->filterScheme($scheme);

        // If the passed scheme is the same as the current scheme, return.. no changes made
        if ($scheme === $this->scheme) {
            return $this;
        }

        // Changes were made. Due to the requirement that a Uri instance is IMMUTABLE.. clone, set and return
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        // First, check to see that the $user parameter is a valid string.
        // If it is not, throw an exception.
        $this->isValidString(__METHOD__, $user);

        // Run the SAME test on the password too
        if ($password !== null) {
            $this->isValidString(__METHOD__, $password);
        }

        // Filter out and calculate the new userInfo
        $userInfo = $this->filterUserInfo($user);

        if ((empty($user) === false) && (empty($password) === false)) {
            $userInfo .= ':' . $this->filterUserInfo($password);
        }

        // If the userInfo is identical to the existing userInfo,
        // simply return the current instance
        if ($userInfo === $this->userInfo) {
            return $this;
        }

        // The userInfo parts are NOT identical
        // Therefore clone and return the new instance
        $clone = clone $this;
        $clone->userInfo = $userInfo;

        return $clone;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        // Check to see that the host is a valid string
        $this->isValidString(__METHOD__, $host);

        // Check to ensure that if the passed $host is identical,
        // simply return the original UriInterface instance
        if ($this->host === $host) {
            return $this;
        }

        // Honor the immutability requirement
        // and clone the existing UriInterface
        $clone = clone $this;
        $clone->host = strtolower($host);

        return $clone;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        if (!is_numeric($port) && $port !== null) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port "%s" specified; must be an integer, an integer string, or null',
                (is_object($port) ? get_class($port) : gettype($port))
            ));
        }
        if ($port !== null) {
            $port = (int)$port;
        }
        if ($port === $this->port) {
            // Do nothing if no change was made.
            return $this;
        }
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid port "%d" specified; must be a valid TCP/UDP port',
                $port
            ));
        }

        // Honor the immutability requirement
        // and clone the existing UriInterface
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException(
                'Invalid path provided; must be a string'
            );
        }

        if (strpos($path, '?') !== false) {
            throw new InvalidArgumentException(
                'Invalid path provided; must not contain a query string'
            );
        }

        if (strpos($path, '#') !== false) {
            throw new InvalidArgumentException(
                'Invalid path provided; must not contain a URI fragment'
            );
        }

        $path = $this->filterPath($path);

        if ($path === $this->path) {
            // Do nothing if no change was made.
            return $this;
        }

        // Honor the immutability requirement
        // and clone the existing UriInterface
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    /**
     * When an object is cloned, PHP 5 will perform a shallow copy of all of the object's properties.
     * Any properties that are references to other variables, will remain references.
     * Once the cloning is complete, if a __clone() method is defined,
     * then the newly created object's __clone() method will be called, to allow any necessary properties that need to be changed.
     * NOT CALLABLE DIRECTLY.
     *
     * CONSIDERING THAT CLONING IS GENERALY USED DURING PERMUTATION,
     * WE CAN SIMPLY RESET THE $uriString PROPERTY SO THAT IT CAN
     * BE RE-CALCUMATED.
     *
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.cloning.php
     * @TODO: RESET $uriString cache!
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * Filters the path of a URI to ensure it is properly encoded.
     *
     * @param string $path
     * @return string
     */
    private function filterPath($path)
    {
        $path = preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . ')(:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/u',
            [$this, 'urlEncodeChar'],
            $path
        );

        if (empty($path)) {
            // No path
            return $path;
        }

        // TODO: Investigate if this is the fastest way to do this?!?
        if ($path[0] !== '/') {
            // Relative path
            return $path;
        }

        // Ensure only one leading slash, to prevent XSS attempts.
        $path = '/' . ltrim($path, '/');

        return $path;
    }

    /**
     * Filters the requested scheme against the
     * list of allowed/valid schemes available.
     *
     * @param string $scheme
     * @return string|UriInterface
     */
    private function filterScheme($scheme)
    {
        // First convert the $scheme to lower-case and strip out additional characters (://)
        $scheme = strtolower($scheme);
        $scheme = preg_replace('#:(//)?$#', '', $scheme);

        // Simply return if the re-formatted $scheme is empty
        if (empty($scheme)) {
            return '';
        }

        // Check to see if the specified scheme exists in the $allowedSchemes list
        if (array_key_exists($scheme, $this->allowedSchemes) === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported scheme requested "%s"; must be empty or in the set (%s)',
                    $scheme,
                    implode(', ', array_keys($this->allowedSchemes))
                )
            );
        }

        return $scheme;
    }

    /**
     * Filters a part of user info in a URI to ensure it is properly encoded.
     *
     * @param string $part
     * @return string
     */
    private function filterUserInfo($part)
    {
        // Note the addition of `%` to initial charset; this allows `|` portion
        // to match and thus prevent double-encoding.
        $pattern = '/(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']+|%(?![A-Fa-f0-9]{2}))/u';

        $return = preg_replace_callback(
            $pattern,
            [$this, 'urlEncodeChar'],
            $part
        );

        return $return;
    }

    /**
     * Performs an internal test
     * to ensure wether or not a given port
     * is standard.
     *
     * @param $scheme
     * @param $host
     * @param $port
     * @return bool
     */
    private function isNonStandardPort($scheme, $host, $port)
    {
        // If the scheme is present, perform additional checks
        if (!$scheme) {
            // Return false if the $host is present but NOT the $port
            if (($host) && (!$port)) {
                return false;
            }

            return true;
        }

        // Return false if BOTH $host and $port are empty
        if ((!$host) && (!$port)) {
            return false;
        }

        // Check to ensure that the $port exists in
        // our list of available schemes.
        return !((isset($this->allowedSchemes[$scheme]) === true) || ($port !== $this->allowedSchemes[$scheme]));
    }

    /**
     * Internal test to ensure that
     * a given param is a valid PHP string.
     *
     * @param string $method
     * @param string $param
     * @throws InvalidArgumentException
     */
    private function isValidString($method, $param)
    {
        // Check to see that the $param parameter is a valid string.
        // If it is not, throw an exception.
        if (is_string($param) === false) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s expects a string argument; received %s',
                    $method,
                    (is_object($param)) ? get_class($param) : gettype($param)
                )
            );
        }
    }

    /**
     * URL encode a character returned by a regex.
     *
     * @param array $matches
     * @return string
     */
    private function urlEncodeChar(array $matches)
    {
        return rawurlencode($matches[0]);
    }

    /**
     * Break up a Uri string into
     * it's separate components and
     * set our Uri properties.
     *
     * @param $uri
     */
    private function parseUri($uri)
    {
        // Use parse_uri to break up the $uri
        $parts = parse_url($uri);

        // Check to see if we have an invalid uri
        if (false === $parts) {
            throw new InvalidArgumentException('Invalid source URI present. Uri appears to be malformed');
        }

        // Set our properties
        $this->scheme = (isset($parts['scheme'])) ? $this->filterScheme($parts['scheme']) : '';
        $this->userInfo = (isset($parts['user'])) ? $this->filterUserInfo($parts['user']) : '';
        $this->host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
        $this->port = (isset($parts['port'])) ? (int)$parts['port'] : null;
        $this->path = (isset($parts['path'])) ? $this->filterPath($parts['path']) : '';

        // Check to see if the password was included too
        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }
}
