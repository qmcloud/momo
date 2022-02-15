package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	"k8s.io/apimachinery/pkg/util/intstr"

	//coreV1 "k8s.io/api/core/v1"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	//"k8s.io/client-go/kubernetes/typed/extensions/v1beta1"
	exv1beta "k8s.io/api/extensions/v1beta1"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type CreateIngressLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewCreateIngressLogic(ctx context.Context, svcCtx *svc.ServiceContext) CreateIngressLogic {
	return CreateIngressLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *CreateIngressLogic) CreateIngress(req types.RequestCreateIngress) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	ingress := &exv1beta.Ingress{
		ObjectMeta: metav1.ObjectMeta{
			Name:      req.Name,
			Namespace: req.Namespace,
		},
		Spec: exv1beta.IngressSpec{
			Rules: []exv1beta.IngressRule{
				exv1beta.IngressRule{
					Host: req.Host,
					IngressRuleValue: exv1beta.IngressRuleValue{
						HTTP: &exv1beta.HTTPIngressRuleValue{
							Paths: []exv1beta.HTTPIngressPath{
								exv1beta.HTTPIngressPath{
									Backend: exv1beta.IngressBackend{
										ServiceName: "nginx",
										ServicePort: intstr.IntOrString{
											Type:   intstr.Int,
											IntVal: 98,
										},
									},
								},
							},
						},
					},
				},
			},
		},
	}
	createService, err := clientSet.ExtensionsV1beta1().Ingresses(req.Namespace).Create(context.TODO(), ingress, metav1.CreateOptions{})
	return nil, errorx.NewCodeError(200, "查询成功", createService)
}
