import React from 'react'

const className = (...arr) => arr.filter(Boolean).join(' ')

export const Field = React.forwardRef(({help, name, children, error, onChange, required, minLength}, ref) => {
    if(error){
        help = error
    }
    return  <div className="row p-b-25">
                    <div className={className('col-12 p-b-5', error && 'has-error')}>
                        <label className="stext-102 cl3" htmlFor={name}>{children}</label>
                        <textarea ref={ref} className="size-110 bor8 stext-102 cl2 p-lr-20 p-tb-10" id={name} name={name} onChange={onChange} required={required} />
                        {help && <div className="help-block">{help}</div>}
                    </div>
                </div>
})