Becklyn Rad Bundle
==================

This bundle provides RAD related functionality for the usage in Symfony.


AJAX Protocol
=============

This bundle uses a default AJAX protocol, that is used in the `AjaxResponseBuilder` and can be used for your
project. The ajax call will always return an error 200, as it shouldn't flood the error tracking (with error 400
AJAX request).

The protocol looks like this:

```typescript
interface AjaxResponse
{
    /**
     * Whether the call succeeded.
     */
    ok: boolean;

    /**
     * Any string status, like "ok" or "invalid-id" that
     * you can react to in your code (if you need to).
     */
    status: string;

    /**
     * The response data.
     */
    data: Record<number|string, any> | Array<any>;

    /**
     * A redirect target, where the AJAX handler should 
     * redirect to.
     */
    redirect?: string;

    /**
     * A toast message with optional type and action target.
     */
    message?: {
        text: string;
        type: "positive" | "negative" | null;
        action?: {
            label: string;
            url: string;
        };
    };
}
```

There is a corresponding fetch client implementation in [`mojave`](https://github.com/Becklyn/mojave) that can be used.
This type above is also available as generic TypeScript type in `mojave`.
