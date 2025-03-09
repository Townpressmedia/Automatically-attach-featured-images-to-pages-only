/** Start
 * Automatically attach featured images to pages only
 * so they don't appear as "unattached" in the media library.
 * Checks and reattaches on both page creation and updates.
 * Helps with deleting unattached images
 * Add to theme functions file
 *
 * @author Created by Guardian Digital Group <https://guardiandigitalgroup.com>
 * @param int     $post_id The ID of the page being saved/updated.
 * @param WP_Post $post    The page object.
 * @param bool    $update  Whether this is an update (true) or a new page (false).
 */
function auto_attach_featured_image_to_pages( $post_id, $post, $update ) {
    // Skip if this is an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Only run for pages
    if ( 'page' !== $post->post_type ) {
        return;
    }

    // Check if a featured image is set for the page
    if ( has_post_thumbnail( $post_id ) ) {
        $featured_image_id = get_post_thumbnail_id( $post_id );

        // Get the current post_parent of the featured image
        $attachment = get_post( $featured_image_id );
        $current_parent = $attachment ? $attachment->post_parent : 0;

        // On page creation or update, ensure the featured image is attached to this page
        // If unattached (post_parent is 0) or attached to a different post, update the post_parent
        if ( $current_parent == 0 || $current_parent != $post_id ) {
            wp_update_post( array(
                'ID'          => $featured_image_id,
                'post_parent' => $post_id,
            ) );
        }
    }
}

   
// Hook into save_post to attach featured image when a page is saved or updated
add_action( 'save_post', 'auto_attach_featured_image_to_pages', 10, 3 );

/** End */
