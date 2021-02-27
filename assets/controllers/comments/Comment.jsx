import {render, unmountComponentAtNode} from 'react-dom'
import React, {useCallback, useEffect, useRef, useState} from 'react'
import {useFetch, usePaginatedFetch} from "./hooks";
import {Icon} from "../components/Icon";
import {Field} from "../components/Form";

function Comments({product, customer, counter}) {
    const {items: comments, setItems: setComments, load, loading, count, hasMore} = usePaginatedFetch('/api/comments?product=' + product)
    counter(count)
    const addComment = useCallback(comment => {
        setComments(comments => [comment, ...comments])
    }, [])
    const deleteComment = useCallback(comment => {
        setComments(comments => comments.filter(c => c !== comment))
    }, [])
    const updateComment = useCallback((newComment, oldComment) => {
        setComments(comments => comments.map(c => c === oldComment ? newComment : c))
    }, [])

    useEffect(() => {
        load()
    }, [])
    return <div>
        {comments.map(c =>
            <Comment
                key={c.id}
                comment={c}
                canEdit={c.author.id === customer}
                onDelete={deleteComment}
                onUpdate={updateComment}
            />
        )}
        {customer && <CommentForm product={product} onComment={addComment}/>}
        {hasMore && <button disabled={loading} className="btn btn-primary" onClick={load}>Load More</button>}
    </div>
}

const dateFormat = {
    dateStyle: 'medium',
    timeStyle: 'short'
}

const VIEW = 'VIEW'
const EDIT = 'EDIT'

const Comment = React.memo(({comment, onDelete, canEdit, onUpdate}) => {
    const date = new Date(comment.createdAt)

    const [state, setState] = useState(VIEW)

    const toggleEdit = useCallback(() => {
        setState(state => state === VIEW ? EDIT : VIEW)
    }, [])
    const onDeleteCallback = useCallback(() => {
        onDelete(comment)
    }, [comment])
    const onComment = useCallback((newComment) => {
        onUpdate(newComment, comment)
        toggleEdit()
    }, [comment])

    const {loading: loadingDelete, load: callDelete} = useFetch(comment['@id'], 'DELETE', onDeleteCallback)
    const rating = parseInt(comment.rating);
    return <div className="flex-w flex-t p-b-68">
        <div className="wrap-pic-s size-109 bor0 of-hidden m-r-18 m-t-6">
            <img src={'/media/customers/avatar.jpg'} alt="AVATAR"/>
        </div>
        <div className="size-207">
            <div className="flex-w flex-sb-m p-b-17">
                            <span className="mtext-107 cl2 p-r-20">
                                {comment.author.fullName}
                            </span>

                <span className="fs-18 cl11">
                                <i className={rating < 1 ? "zmdi zmdi-star-outline" : "zmdi zmdi-star"}></i>
                                <i className={rating < 2 ? "zmdi zmdi-star-outline" : "zmdi zmdi-star"}></i>
                                <i className={rating < 3 ? "zmdi zmdi-star-outline" : "zmdi zmdi-star"}></i>
                                <i className={rating < 4 ? "zmdi zmdi-star-outline" : "zmdi zmdi-star"}></i>
                                <i className={rating < 5 ? "zmdi zmdi-star-outline" : "zmdi zmdi-star"}></i>
                            </span>
            </div>
            {state === VIEW ?
                <p className="stext-102 cl6">{comment.review}</p> :
                <CommentForm comment={comment} onComment={onComment} onCancel={toggleEdit}/>
            }

            <p className="text-muted">
                {date.toLocaleString(undefined, dateFormat)}
            </p>
            {(canEdit && state !== EDIT) && <p>
                <button className="btn btn-danger" onClick={callDelete.bind(this, null)} disabled={loadingDelete}>
                    <Icon icon="trash"/> Delete
                </button>
                <button className="btn btn-secondary" onClick={toggleEdit}>
                    <Icon icon="trash"/> Update
                </button>
            </p>}
        </div>
    </div>
})

const CommentForm = React.memo(({product = null, onComment, comment = null, onCancel}) => {
    //Variables
    const ref = useRef(null)

    // Methods
    const onSuccess = useCallback(comment => {
        onComment(comment)
        ref.current.value = ' '
    }, [ref, onComment])


    // Hooks

    const method = comment ? 'PUT' : 'POST'
    const url = comment ? comment['@id'] : '/api/comments'
    const {load, loading, errors, clearError} = useFetch(url, method, onSuccess)
    const onSubmit = useCallback(e => {
        e.preventDefault()
        load({
            review: ref.current.value,
            product: "/api/stocks/" + product,
            rating: 5

        })
    }, [load, ref, product])

    //Effect
    useEffect(() => {
        if (comment && comment.review && ref.current) {
            ref.current.value = comment.review
        }
    }, [comment, ref])

    return <form className="w-full" onSubmit={onSubmit}>
        {comment === null && <h5 className="mtext-108 cl2 p-b-7">
            Add a review
        </h5>}

        <p className="stext-102 cl6">
            Your email address will not be published. Required fields are marked *
        </p>

        <div className="flex-w flex-m p-t-50 p-b-23">
                        <span className="stext-102 cl3 m-r-16">
                            Your Rating
                        </span>

            <span className="wrap-rating fs-18 cl11 pointer">
                            <i className="item-rating pointer zmdi zmdi-star-outline"></i>
                            <i className="item-rating pointer zmdi zmdi-star-outline"></i>
                            <i className="item-rating pointer zmdi zmdi-star-outline"></i>
                            <i className="item-rating pointer zmdi zmdi-star-outline"></i>
                            <i className="item-rating pointer zmdi zmdi-star-outline"></i>
                            <input className="dis-none" type="number" name="rating"/>
                        </span>
        </div>

        <Field
            name="review"
            help="comments that do not comply with our code of conduct will be moderated."
            ref={ref}
            required
            minLength={5}
            onChange={clearError.bind(this, 'review')}
            error={errors['review']}
        >Your review</Field>

        <button className="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10"
                disabled={loading}>
            <Icon icon="paper-plane"/> {comment === null ? '  Submit' : '  Update'}
        </button>
        {onCancel && <button className="btn btn-secondary" onClick={onCancel}>
            Cancel
        </button>}
    </form>
})


function TabHeader({name, count}) {


    return <li className="nav-item p-b-10">
        <a className="nav-link" data-toggle="tab" href={"#" + name} role="tab">
            <Icon icon="comments"/>
            {name} {count && '(' + count + ')'}
        </a>
    </li>
}

function App({product, customer}) {
   const [count, setCount] =  useState(0)
return <div className="bor10 m-t-50 p-t-43 p-b-40">
    <div className="tab01">
        <ul className="nav nav-tabs" role="tablist">
            <TabHeader name="description"/>
            <TabHeader name="information"/>
            <TabHeader name="reviews" count={count}/>
        </ul>
        <div className="tab-content p-t-43">
            <div className="tab-pane fade " id="description" role="tabpanel">
                <div className="how-pos2 p-lr-15-md">
                    <p className="stext-102 cl6">
                        Aenean sit amet gravida nisi. Nam fermentum est felis, quis feugiat nunc fringilla
                        sit amet. Ut in blandit ipsum. Quisque luctus dui at ante aliquet, in hendrerit
                        lectus interdum. Morbi elementum sapien rhoncus pretium maximus. Nulla lectus enim,
                        cursus et elementum sed, sodales vitae eros. Ut ex quam, porta consequat interdum
                        in, faucibus eu velit. Quisque rhoncus ex ac libero varius molestie. Aenean tempor
                        sit amet orci nec iaculis. Cras sit amet nulla libero. Curabitur dignissim, nunc nec
                        laoreet consequat, purus nunc porta lacus, vel efficitur tellus augue in ipsum. Cras
                        in arcu sed metus rutrum iaculis. Nulla non tempor erat. Duis in egestas nunc.
                    </p>
                </div>
            </div>
            <div className="tab-pane fade" id="information" role="tabpanel">
                <div className="row">
                    <div className="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                        <ul className="p-lr-28 p-lr-15-sm">
                            <li className="flex-w flex-t p-b-7">
                                                    <span className="stext-102 cl3 size-205">
                                                        Weight
                                                    </span>

                                <span className="stext-102 cl6 size-206">
                                                        0.79 kg
                                                    </span>
                            </li>

                            <li className="flex-w flex-t p-b-7">
                                                    <span className="stext-102 cl3 size-205">
                                                        Dimensions
                                                    </span>

                                <span className="stext-102 cl6 size-206">
                                                        110 x 33 x 100 cm
                                                    </span>
                            </li>

                            <li className="flex-w flex-t p-b-7">
                                                    <span className="stext-102 cl3 size-205">
                                                        Materials
                                                    </span>

                                <span className="stext-102 cl6 size-206">
                                                        60% cotton
                                                    </span>
                            </li>

                            <li className="flex-w flex-t p-b-7">
                                                    <span className="stext-102 cl3 size-205">
                                                        Color
                                                    </span>

                                <span className="stext-102 cl6 size-206">
                                                        Black, Blue, Grey, Green, Red, White
                                                    </span>
                            </li>

                            <li className="flex-w flex-t p-b-7">
                                                    <span className="stext-102 cl3 size-205">
                                                        Size
                                                    </span>

                                <span className="stext-102 cl6 size-206">
                                                        XL, L, M, S
                                                    </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div className="tab-pane fade show active" id="reviews" role="tabpanel">
                <div className="row">
                    <div className="col-sm-10 col-md-8 col-lg-6 m-lr-auto">

                        <Comments product={product} customer={customer} counter={setCount}/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
}

class CommentsElement extends HTMLElement {

    connectedCallback() {

        const product = parseInt(this.dataset.product, 10)
        const customer = parseInt(this.dataset.customer, 10) || null
        render(
            <App product={product} customer={customer} />
            , this)

    }

    disconnectedCallback() {
        unmountComponentAtNode(this)
    }
}

customElements.define('product-comments', CommentsElement)